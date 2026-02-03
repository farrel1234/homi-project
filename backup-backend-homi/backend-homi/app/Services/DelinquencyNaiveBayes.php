<?php

namespace App\Services;

use App\Models\MlNbModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DelinquencyNaiveBayes
{
    /**
     * Train NB model dan simpan ke ml_nb_models.
     * Output JSON sengaja dibuat kompatibel dengan command scorer kamu:
     * { name, trained_at, grace_days, priors: [p0,p1], likelihood: {...} }
     */
    public function train(int $months = 12, int $grace = 7, string $name = 'delinquency_nb_v1'): array
    {
        $alpha = 1.0; // Laplace smoothing

        // Train dari bulan-bulan yang sudah lewat (hindari bulan berjalan)
        $end = now()->startOfMonth()->subMonth(); // bulan terakhir yang lengkap
        $start = (clone $end)->subMonths(max(0, $months - 1))->startOfMonth();

        // Ambil user role resident
        $userIds = DB::table('users')->where('role', 'resident')->pluck('id')->all();

        // fitur yang kita latih (harus sama dengan scorer)
        $bernoulli = ['unpaid_3m', 'late_3m', 'avg_late_6m'];
        $categorical = [
            'last_status'   => ['paid_on_time', 'paid_late', 'unpaid'],
            'amount_bucket' => ['<=50k', '50-150k', '>150k'],
        ];

        $samples = [];
        $now = now();

        // Loop tiap user dan tiap period
        for ($p = (clone $start); $p <= $end; $p->addMonth()) {
            $periodStart = (clone $p)->startOfMonth();

            foreach ($userIds as $uid) {
                $inv = DB::table('fee_invoices')
                    ->select('id', 'status', 'period', 'due_date')
                    ->where('user_id', (int)$uid)
                    ->whereDate('period', '=', $periodStart->toDateString())
                    ->first();

                if (!$inv) continue;

                $due = $inv->due_date
                    ? Carbon::parse($inv->due_date)
                    : (clone $periodStart)->endOfMonth();

                $deadline = (clone $due)->addDays($grace);

                // belum "matang" → jangan dipakai training
                if ($now->lessThanOrEqualTo($deadline)) {
                    continue;
                }

                // cari payment approved paling awal
                $paidAtStr = DB::table('fee_payments')
                    ->where('invoice_id', (int)$inv->id)
                    ->where('review_status', 'approved')
                    ->min('created_at');

                $paidAt = $paidAtStr ? Carbon::parse($paidAtStr) : null;

                // label delinquent:
                // 1 jika unpaid setelah grace ATAU paid tapi lewat deadline
                $y = 0;
                if (!$paidAt) {
                    $isUnpaid = in_array($inv->status, ['unpaid', 'pending'], true);
                    $y = $isUnpaid ? 1 : 0;
                } else {
                    $y = $paidAt->greaterThan($deadline) ? 1 : 0;
                }

                // fitur dihitung berdasarkan histori sebelum periodStart (anti kebocoran)
                $asOf = (clone $periodStart)->subDay();
                [, $x] = $this->computeFeatures((int)$uid, $periodStart, $grace, $asOf);

                $samples[] = ['y' => $y, 'x' => $x];
            }
        }

        // Hitung counts kelas
        $c0 = 0; $c1 = 0;
        foreach ($samples as $s) {
            if ((int)$s['y'] === 1) $c1++; else $c0++;
        }

        // Priors dengan smoothing (ini yang bikin nggak jadi [0,1] mentok)
        $n = max(1, $c0 + $c1);
        $p0 = ($c0 + $alpha) / ($n + 2 * $alpha);
        $p1 = ($c1 + $alpha) / ($n + 2 * $alpha);

        // Init counters likelihood
        $bernCount1 = [
            0 => array_fill_keys($bernoulli, 0),
            1 => array_fill_keys($bernoulli, 0),
        ];
        $classN = [0 => 0, 1 => 0];

        $catCounts = [
            0 => ['last_status' => [], 'amount_bucket' => []],
            1 => ['last_status' => [], 'amount_bucket' => []],
        ];

        foreach ($samples as $s) {
            $y = (int)$s['y'];
            $x = (array)$s['x'];
            $classN[$y]++;

            // bernoulli
            foreach ($bernoulli as $f) {
                $val = (int)($x[$f] ?? 0);
                if ($val === 1) $bernCount1[$y][$f]++;
            }

            // categorical
            foreach ($categorical as $f => $cats) {
                $val = (string)($x[$f] ?? '');
                if ($val === '') continue;
                $catCounts[$y][$f][$val] = ($catCounts[$y][$f][$val] ?? 0) + 1;
            }
        }

        // Build likelihood format yang cocok dengan scorer kamu
        $likelihood = [];

        // Bernoulli: [[p(x=1|0)],[p(x=1|1)]]
        $likelihood = [];

        foreach ($bernoulli as $f) {
            $n0 = max(0, (int)($classN[0] ?? 0));
            $n1 = max(0, (int)($classN[1] ?? 0));

            $c10 = (int)($bernCount1[0][$f] ?? 0);
            $c11 = (int)($bernCount1[1][$f] ?? 0);

            $p10 = ($c10 + $alpha) / (max(1, $n0) + 2 * $alpha);
            $p11 = ($c11 + $alpha) / (max(1, $n1) + 2 * $alpha);

            // clamp biar ga log(0) dan ga “kunci” jadi 1.0000
            $eps = 1e-9;
            $p10 = max($eps, min(1 - $eps, $p10));
            $p11 = max($eps, min(1 - $eps, $p11));

            $likelihood[$f] = [[(float)$p10], [(float)$p11]];
        }


        // (Fix kecil: baris di atas salah ketik "note:" kalau copas. Pakai versi benar di bawah)
        // === versi benar bernoulli ===
        $likelihood = [];
        foreach ($bernoulli as $f) {
            $n0 = max(0, $classN[0]);
            $n1 = max(0, $classN[1]);

            $p10 = ($bernCount1[0][$f] + $alpha) / (max(1, $n0) + 2 * $alpha);
            $p11 = ($bernCount1[1][$f] + $alpha) / (max(1, $n1) + 2 * $alpha);

            $likelihood[$f] = [[(float)$p10], [(float)$p11]];
        }

        // Categorical: [ {cat->prob|class0}, {cat->prob|class1} ]
        foreach ($categorical as $f => $cats) {
            $map0 = [];
            $map1 = [];

            $n0 = max(1, $classN[0]);
            $n1 = max(1, $classN[1]);
            $k  = max(1, count($cats));

            foreach ($cats as $cat) {
                $cnt0 = (int)($catCounts[0][$f][$cat] ?? 0);
                $cnt1 = (int)($catCounts[1][$f][$cat] ?? 0);

                $map0[$cat] = ($cnt0 + $alpha) / ($n0 + $alpha * $k);
                $map1[$cat] = ($cnt1 + $alpha) / ($n1 + $alpha * $k);
            }

            $likelihood[$f] = [$map0, $map1];
        }

        $model = [
            'name'       => $name,
            'trained_at' => now()->toISOString(),
            'grace_days' => $grace,
            'priors'     => [(float)$p0, (float)$p1],
            'likelihood' => $likelihood,
            'meta'       => [
                'samples' => count($samples),
                'class0'  => $c0,
                'class1'  => $c1,
                'window'  => [$start->format('Y-m'), $end->format('Y-m')],
            ],
        ];

        // simpan
        MlNbModel::query()->updateOrCreate(
            ['name' => $name],
            ['model_json' => $model]
        );

        return $model;
    }

    /**
     * Compute fitur dengan logika yang sejalan dengan scorer kamu,
     * tapi bisa pakai asOf untuk anti leakage.
     */
    private function computeFeatures(int $userId, Carbon $periodStart, int $grace, Carbon $asOf): array
    {
        $w3_from = (clone $periodStart)->subMonths(3)->startOfMonth();
        $w6_from = (clone $periodStart)->subMonths(6)->startOfMonth();
        $to      = (clone $periodStart)->subDay();

        $invoices6 = DB::table('fee_invoices')
            ->select('id', 'status', 'period', 'due_date', 'amount')
            ->where('user_id', $userId)
            ->whereDate('period', '>=', $w6_from->toDateString())
            ->whereDate('period', '<=', $to->toDateString())
            ->orderBy('period', 'asc')
            ->get();

        $invoiceIds6 = $invoices6->pluck('id')->all();

        // paidAtMap yang hanya diketahui <= asOf
        $paidAtMap = [];
        if (!empty($invoiceIds6)) {
            $pays = DB::table('fee_payments')
                ->select('invoice_id', DB::raw('MIN(created_at) as paid_at'))
                ->whereIn('invoice_id', $invoiceIds6)
                ->where('review_status', 'approved')
                ->where('created_at', '<=', $asOf->toDateTimeString())
                ->groupBy('invoice_id')
                ->get();

            foreach ($pays as $p) {
                $paidAtMap[(int)$p->invoice_id] = Carbon::parse($p->paid_at);
            }
        }

        $invoices3 = $invoices6->filter(fn($inv) => Carbon::parse($inv->period)->greaterThanOrEqualTo($w3_from))->values();

        $overdueUnpaidCount3 = 0;
        $latePaidCount3 = 0;

        foreach ($invoices3 as $inv) {
            $due = $inv->due_date ? Carbon::parse($inv->due_date) : Carbon::parse($inv->period)->endOfMonth();
            $deadline = (clone $due)->addDays($grace);
            $paidAt = $paidAtMap[(int)$inv->id] ?? null;

            if ($paidAt) {
                if ($paidAt->greaterThan($deadline)) $latePaidCount3++;
            } else {
                $isUnpaid = in_array($inv->status, ['unpaid', 'pending'], true);
                if ($isUnpaid && $asOf->greaterThan($deadline)) $overdueUnpaidCount3++;
            }
        }

        // avg late days 6 bulan (yang diketahui <= asOf)
        $lateDaysSum = 0; $lateDaysN = 0;
        foreach ($invoices6 as $inv) {
            $paidAt = $paidAtMap[(int)$inv->id] ?? null;
            if (!$paidAt) continue;

            $due = $inv->due_date ? Carbon::parse($inv->due_date) : Carbon::parse($inv->period)->endOfMonth();
            $deadline = (clone $due)->addDays($grace);

            if ($paidAt->greaterThan($deadline)) {
                $lateDaysSum += $deadline->diffInDays($paidAt);
                $lateDaysN++;
            }
        }
        $avgLateDays6 = $lateDaysN > 0 ? ($lateDaysSum / $lateDaysN) : 0.0;

        // last_status (invoice terakhir sebelum periodStart)
        $lastInv = DB::table('fee_invoices')
            ->select('id', 'status', 'period', 'due_date')
            ->where('user_id', $userId)
            ->whereDate('period', '<', $periodStart->toDateString())
            ->orderBy('period', 'desc')
            ->first();

        $lastStatus = 'paid_on_time';
        if ($lastInv) {
            $due = $lastInv->due_date ? Carbon::parse($lastInv->due_date) : Carbon::parse($lastInv->period)->endOfMonth();
            $deadline = (clone $due)->addDays($grace);
            $paidAt = $paidAtMap[(int)$lastInv->id] ?? null;

            if (!$paidAt) {
                $isUnpaid = in_array($lastInv->status, ['unpaid', 'pending'], true);
                $lastStatus = ($isUnpaid && $asOf->greaterThan($deadline)) ? 'unpaid' : 'paid_on_time';
            } else {
                $lastStatus = $paidAt->greaterThan($deadline) ? 'paid_late' : 'paid_on_time';
            }
        }

        // amount_bucket (invoice target)
        $targetInv = DB::table('fee_invoices')
            ->select('amount')
            ->where('user_id', $userId)
            ->whereDate('period', '=', $periodStart->toDateString())
            ->first();

        $amount = (int)($targetInv->amount ?? 0);

        $amountBucket = '50-150k';
        if ($amount <= 50000) $amountBucket = '<=50k';
        else if ($amount > 150000) $amountBucket = '>150k';

        $featureValues = [
            'unpaid_3m'     => $overdueUnpaidCount3 > 0 ? 1 : 0,
            'late_3m'       => $latePaidCount3 > 0 ? 1 : 0,
            'avg_late_6m'   => $avgLateDays6 > 0 ? 1 : 0,
            'last_status'   => $lastStatus,
            'amount_bucket' => $amountBucket,
        ];

        $featuresJson = [
            'period' => $periodStart->format('Y-m'),
            'as_of'  => $asOf->toDateTimeString(),
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
}
