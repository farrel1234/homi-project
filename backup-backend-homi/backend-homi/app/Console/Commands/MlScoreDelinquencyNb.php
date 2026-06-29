<?php

namespace App\Console\Commands;

use App\Models\MlNbModel;
use App\Models\PaymentRiskScore;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MlScoreDelinquencyNb extends Command
{
    protected $signature = 'ml:score-delinquency-nb
        {--model=delinquency_nb_v1 : Nama model di ml_nb_models.name}
        {--months=12 : Window histori (bulan) untuk fitur (default 12)}
        {--grace=7 : Grace days (hari) untuk definisi telat}
        {--period= : Periode target (YYYY-MM). Default bulan sekarang}
        {--threshold=0.60 : Ambang prediksi menunggak}
        {--role=resident : Role user yang dihitung}
        {--debug=0 : Print struktur model dan berhenti}
    ';

    protected $description = 'Hitung risk score menunggak per warga dan simpan ke payment_risk_scores';

    public function handle(): int
    {
        $modelName = (string) $this->option('model');
        $months    = (int) $this->option('months');
        $grace     = (int) $this->option('grace');
        $threshold = (float) $this->option('threshold');
        $role      = (string) $this->option('role');
        $debug     = (int) $this->option('debug');

        $periodOpt = $this->option('period');
        $periodStart = $periodOpt
            ? Carbon::createFromFormat('Y-m', (string)$periodOpt)->startOfMonth()
            : now()->startOfMonth();

        /** @var MlNbModel|null $modelRow */
        $modelRow = MlNbModel::query()->where('name', $modelName)->first();
        if (!$modelRow) {
            $this->error("Model '{$modelName}' tidak ditemukan di tabel ml_nb_models.");
            return self::FAILURE;
        }

        // payload model dari DB (pakai accessor yang fleksibel kalau kamu sudah pasang)
        $raw = method_exists($modelRow, 'getModelPayloadAttribute')
            ? ($modelRow->model_payload ?? [])
            : ($modelRow->model_json ?? []);

        if (!is_array($raw)) $raw = [];

        // kadang dibungkus
        $model = $raw;
        if (isset($model['model']) && is_array($model['model'])) $model = $model['model'];
        if (isset($model['nb']) && is_array($model['nb'])) $model = $model['nb'];

        if ($debug === 1) {
            $this->line("=== DEBUG ml_nb_models row ===");
            $this->line("name: ".$modelRow->name);
            $this->line("keys: ".implode(', ', array_keys($model)));
            $this->line("sample(json): ".substr(json_encode($model), 0, 1200).' ...');
            return self::SUCCESS;
        }

        // normalisasi format model kamu (priors + likelihood)
        $nb = $this->normalizeModel($model);

        if (!$nb) {
            $this->error("Model JSON tidak dikenali. Pastikan keys minimal ada: priors & likelihood.");
            $this->line("Coba debug: php artisan ml:score-delinquency-nb --period=".$periodStart->format('Y-m')." --debug=1");
            return self::FAILURE;
        }

        // Ambil user ids
        $userIds = DB::table('users')
            ->when($role !== '', fn($q) => $q->where('role', $role))
            ->pluck('id')
            ->all();

        if (empty($userIds)) {
            $this->warn("Tidak ada user dengan role '{$role}'.");
            return self::SUCCESS;
        }

        $this->info("Scoring period: ".$periodStart->format('Y-m')." | users: ".count($userIds));

        $computedAt = now();

        $bar = $this->output->createProgressBar(count($userIds));
        $bar->start();

        $written = 0;
        foreach ($userIds as $uid) {
            [$featuresJson, $featureValues] = $this->computeFeatures((int)$uid, $periodStart, $months, $grace);

            $prob1 = $this->predictProbDelinquent($nb, $featureValues);

            PaymentRiskScore::query()->updateOrCreate(
                ['user_id' => (int)$uid, 'period' => $periodStart->toDateString()],
                [
                    'risk' => $prob1,
                    'predicted_delinquent' => ($prob1 >= $threshold),
                    'features_json' => $featuresJson,
                    'computed_at' => $computedAt,
                ]
            );

            $written++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("OK scored: {$written} users → payment_risk_scores (period {$periodStart->format('Y-m')})");
        return self::SUCCESS;
    }

    /**
     * Format model kamu:
     * {
     *   priors: [p0, p1]  (contoh: [0,1])
     *   likelihood: {
     *      unpaid_3m: [[p0],[p1]]  -> Bernoulli p(x=1|class)
     *      last_status: [ {unpaid:0.5,...}, {unpaid:0.9,...} ] -> categorical
     *   }
     * }
     */
    private function normalizeModel(array $model): ?array
    {
        if (!isset($model['priors']) || !isset($model['likelihood'])) {
            return null;
        }

        $pri = $model['priors'];
        if (!is_array($pri) || count($pri) < 2) return null;

        $p0 = (float)($pri[0] ?? 0.5);
        $p1 = (float)($pri[1] ?? 0.5);

        // kalau ternyata ini counts, convert
        if ($p0 > 1 || $p1 > 1) {
            $sum = max(1e-9, $p0 + $p1);
            $p0 = $p0 / $sum;
            $p1 = $p1 / $sum;
        }

        // clamp (biar ga log(0))
        $eps = 1e-9;
        $p0 = max($eps, min(1 - $eps, $p0));
        $p1 = max($eps, min(1 - $eps, $p1));

        $like = $model['likelihood'];
        if (!is_array($like)) return null;

        $features = [];

        foreach ($like as $fname => $block) {
            // categorical: [ {..}, {..} ]
            if (is_array($block) && count($block) === 2 && $this->isAssocArray($block[0] ?? null) && $this->isAssocArray($block[1] ?? null)) {
                $features[$fname] = [
                    'type' => 'categorical',
                    'map'  => [
                        '0' => $block[0],
                        '1' => $block[1],
                    ],
                ];
                continue;
            }

            // bernoulli: [[p0],[p1]] atau [p0,p1] atau [[p0],[p1]] campur
            if (is_array($block) && count($block) === 2) {
                $b0 = $block[0];
                $b1 = $block[1];

                $pp0 = is_array($b0) ? (float)($b0[0] ?? 0.5) : (float)$b0;
                $pp1 = is_array($b1) ? (float)($b1[0] ?? 0.5) : (float)$b1;

                $pp0 = max($eps, min(1 - $eps, $pp0));
                $pp1 = max($eps, min(1 - $eps, $pp1));

                $features[$fname] = [
                    'type' => 'bernoulli',
                    'p'    => ['0' => $pp0, '1' => $pp1],
                ];
                continue;
            }

            // kalau ada format lain, skip (biar gak fail total)
        }

        if (empty($features)) return null;

        return [
            'priors'   => ['0' => $p0, '1' => $p1],
            'features' => $features,
            'meta'     => [
                'name' => $model['name'] ?? null,
                'trained_at' => $model['trained_at'] ?? null,
                'grace_days' => $model['grace_days'] ?? null,
            ],
        ];
    }

    private function isAssocArray($v): bool
    {
        if (!is_array($v)) return false;
        $keys = array_keys($v);
        return array_keys($keys) !== $keys; // key tidak berurutan 0..n
    }

    /**
     * Hitung fitur yang NAMANYA sama dengan model:
     * - unpaid_3m (0/1)
     * - late_3m (0/1)
     * - avg_late_6m (0/1) -> avg late days > 0
     * - last_status (unpaid / paid_late / paid_on_time)
     * - amount_bucket (<=50k / 50-150k / >150k)
     */
    private function computeFeatures(int $userId, Carbon $periodStart, int $months, int $grace): array
    {
        $w3_from = (clone $periodStart)->subMonths(3)->startOfMonth();
        $w6_from = (clone $periodStart)->subMonths(6)->startOfMonth();
        $to      = (clone $periodStart)->subDay();

        // Ambil invoice 6 bulan terakhir (buat banyak kebutuhan)
        $invoices6 = DB::table('fee_invoices')
            ->select('id', 'user_id', 'status', 'period', 'due_date', 'amount')
            ->where('user_id', $userId)
            ->whereDate('period', '>=', $w6_from->toDateString())
            ->whereDate('period', '<=', $to->toDateString())
            ->orderBy('period', 'asc')
            ->get();

        $invoiceIds6 = $invoices6->pluck('id')->all();

        // paid_at map (approved)
        $paidAtMap = [];
        if (!empty($invoiceIds6)) {
            $pays = DB::table('fee_payments')
                ->select('invoice_id', DB::raw('MIN(created_at) as paid_at'))
                ->whereIn('invoice_id', $invoiceIds6)
                ->where('review_status', 'approved')
                ->groupBy('invoice_id')
                ->get();

            foreach ($pays as $p) {
                $paidAtMap[(int)$p->invoice_id] = Carbon::parse($p->paid_at);
            }
        }

        // filter 3 bulan dari invoices6
        $invoices3 = $invoices6->filter(function ($inv) use ($w3_from) {
            return Carbon::parse($inv->period)->greaterThanOrEqualTo($w3_from);
        })->values();

        $today = now();

        $overdueUnpaidCount3 = 0;
        $latePaidCount3 = 0;

        foreach ($invoices3 as $inv) {
            $due = $inv->due_date
                ? Carbon::parse($inv->due_date)
                : Carbon::parse($inv->period)->endOfMonth();

            $deadline = (clone $due)->addDays($grace);
            $paidAt = $paidAtMap[(int)$inv->id] ?? null;

            if ($paidAt) {
                if ($paidAt->greaterThan($deadline)) {
                    $latePaidCount3++;
                }
            } else {
                // unpaid yang sudah lewat deadline (as of now)
                $isUnpaid = in_array($inv->status, ['unpaid', 'pending'], true);
                if ($isUnpaid && $today->greaterThan($deadline)) {
                    $overdueUnpaidCount3++;
                }
            }
        }

        // avg late days 6 bulan
        $lateDaysSum = 0;
        $lateDaysN = 0;

        foreach ($invoices6 as $inv) {
            $paidAt = $paidAtMap[(int)$inv->id] ?? null;
            if (!$paidAt) continue;

            $due = $inv->due_date
                ? Carbon::parse($inv->due_date)
                : Carbon::parse($inv->period)->endOfMonth();

            $deadline = (clone $due)->addDays($grace);

            if ($paidAt->greaterThan($deadline)) {
                $lateDaysSum += $deadline->diffInDays($paidAt);
                $lateDaysN++;
            }
        }

        $avgLateDays6 = $lateDaysN > 0 ? ($lateDaysSum / $lateDaysN) : 0.0;

        // last_status: invoice terbaru sebelum periodStart
        $lastInv = DB::table('fee_invoices')
            ->select('id', 'status', 'period', 'due_date')
            ->where('user_id', $userId)
            ->whereDate('period', '<', $periodStart->toDateString())
            ->orderBy('period', 'desc')
            ->first();

        $lastStatus = 'paid_on_time';
        if ($lastInv) {
            $due = $lastInv->due_date
                ? Carbon::parse($lastInv->due_date)
                : Carbon::parse($lastInv->period)->endOfMonth();
            $deadline = (clone $due)->addDays($grace);
            $paidAt = $paidAtMap[(int)$lastInv->id] ?? null;

            if (!$paidAt) {
                $isUnpaid = in_array($lastInv->status, ['unpaid', 'pending'], true);
                $lastStatus = ($isUnpaid && $today->greaterThan($deadline)) ? 'unpaid' : 'paid_on_time';
            } else {
                $lastStatus = $paidAt->greaterThan($deadline) ? 'paid_late' : 'paid_on_time';
            }
        }

        // amount_bucket: ambil invoice periode target (kalau ada)
        $targetInv = DB::table('fee_invoices')
            ->select('amount')
            ->where('user_id', $userId)
            ->whereDate('period', '=', $periodStart->toDateString())
            ->first();

        $amount = (int)($targetInv->amount ?? 0);

        $amountBucket = '50-150k';
        if ($amount <= 50000) $amountBucket = '<=50k';
        else if ($amount > 150000) $amountBucket = '>150k';

        // Feature values sesuai model (bernoulli 0/1 + categorical string)
        $featureValues = [
            'unpaid_3m'    => $overdueUnpaidCount3 > 0 ? 1 : 0,
            'late_3m'      => $latePaidCount3 > 0 ? 1 : 0,
            'avg_late_6m'  => $avgLateDays6 > 0 ? 1 : 0,
            'last_status'  => $lastStatus,
            'amount_bucket'=> $amountBucket,
        ];

        // simpan detail mentah biar gampang debug
        $featuresJson = [
            'period' => $periodStart->format('Y-m'),
            'grace_days' => $grace,
            'window_3m' => [$w3_from->toDateString(), $to->toDateString()],
            'window_6m' => [$w6_from->toDateString(), $to->toDateString()],
            'raw' => [
                'overdue_unpaid_count_3m' => $overdueUnpaidCount3,
                'late_paid_count_3m'      => $latePaidCount3,
                'avg_late_days_6m'        => $avgLateDays6,
                'target_amount'           => $amount,
            ],
            'values' => $featureValues,
        ];

        return [$featuresJson, $featureValues];
    }

    private function predictProbDelinquent(array $nb, array $featureValues): float
    {
        $eps = 1e-12;

        $log0 = log(max($eps, (float)($nb['priors']['0'] ?? 0.5)));
        $log1 = log(max($eps, (float)($nb['priors']['1'] ?? 0.5)));

        $features = $nb['features'] ?? [];

        foreach ($features as $fname => $spec) {
            if (!array_key_exists($fname, $featureValues)) continue;

            $type = $spec['type'] ?? null;

            if ($type === 'bernoulli') {
                $x = (int)$featureValues[$fname]; // 0/1
                $p0 = (float)($spec['p']['0'] ?? 0.5);
                $p1 = (float)($spec['p']['1'] ?? 0.5);

                $p0 = max(1e-9, min(1 - 1e-9, $p0));
                $p1 = max(1e-9, min(1 - 1e-9, $p1));

                $prob0 = ($x === 1) ? $p0 : (1 - $p0);
                $prob1 = ($x === 1) ? $p1 : (1 - $p1);

                $log0 += log(max($eps, $prob0));
                $log1 += log(max($eps, $prob1));
                continue;
            }

            if ($type === 'categorical') {
                $val = (string)$featureValues[$fname];
                $m0 = (array)($spec['map']['0'] ?? []);
                $m1 = (array)($spec['map']['1'] ?? []);

                // fallback kalau value ga ada di model
                $fallback0 = 1 / max(2, count($m0));
                $fallback1 = 1 / max(2, count($m1));

                $prob0 = (float)($m0[$val] ?? $fallback0);
                $prob1 = (float)($m1[$val] ?? $fallback1);

                $log0 += log(max($eps, $prob0));
                $log1 += log(max($eps, $prob1));
                continue;
            }
        }

        // softmax log
        $m = max($log0, $log1);
        $p0 = exp($log0 - $m);
        $p1 = exp($log1 - $m);
        $sum = $p0 + $p1;

        return $sum > 0 ? ($p1 / $sum) : 0.5;
    }
}
