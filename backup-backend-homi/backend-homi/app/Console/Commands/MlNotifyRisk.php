<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\PaymentRiskScore;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class MlNotifyRisk extends Command
{
    protected $signature = 'ml:notify-risk
        {--period= : Periode target (YYYY-MM). Default bulan sekarang}
        {--threshold=0.60 : Ambang risk untuk dikirim notifikasi}
        {--only-flagged=1 : Jika 1, hanya yang predicted_delinquent=1}
    ';

    protected $description = 'Buat notifikasi ke warga yang berisiko menunggak berdasarkan payment_risk_scores';

    public function handle(): int
    {
        $periodOpt   = $this->option('period');
        $threshold   = (float) $this->option('threshold');
        $onlyFlagged = (int) $this->option('only-flagged');

        $periodStart = $periodOpt
            ? Carbon::createFromFormat('Y-m', (string)$periodOpt)->startOfMonth()
            : now()->startOfMonth();

        $periodKey = $periodStart->format('Y-m');

        // Cari nama kolom JSON yang tersedia (kalau ada)
        $jsonCol = collect(['meta_json', 'meta', 'data', 'payload', 'metadata', 'extra'])
            ->first(fn ($c) => Schema::hasColumn('app_notifications', $c));

        // Cari kolom title/body yang tersedia (biar fleksibel)
        $titleCol = Schema::hasColumn('app_notifications', 'title')
            ? 'title'
            : (Schema::hasColumn('app_notifications', 'subject') ? 'subject' : 'title');

        $bodyCol = Schema::hasColumn('app_notifications', 'body')
            ? 'body'
            : (Schema::hasColumn('app_notifications', 'message')
                ? 'message'
                : (Schema::hasColumn('app_notifications', 'content') ? 'content' : 'body'));

        $q = PaymentRiskScore::query()
            ->whereDate('period', $periodStart->toDateString())
            ->orderByDesc('risk');

        if ($onlyFlagged === 1) {
            $q->where('predicted_delinquent', 1);
        } else {
            $q->where('risk', '>=', $threshold);
        }

        $rows = $q->get();

        if ($rows->isEmpty()) {
            $this->warn("Tidak ada risk score untuk dikirimi notif pada period {$periodKey}.");
            return self::SUCCESS;
        }

        $created = 0;

        foreach ($rows as $r) {
            $title = 'Pengingat Iuran Bulan ' . $periodStart->translatedFormat('F Y');

            // Anti duplikat:
            $existsQ = AppNotification::query()
                ->where('user_id', $r->user_id)
                ->where('type', 'risk_warning');

            if ($jsonCol) {
                // JSON column syntax Laravel => kolom->key
                $existsQ->where($jsonCol . '->period', $periodKey);
            } else {
                // fallback kalau tidak ada kolom JSON: cek via title yang sudah mengandung period
                $existsQ->where($titleCol, $title);
            }

            if ($existsQ->exists()) {
                continue;
            }

            $payload = [
                'user_id' => $r->user_id,
                'type'    => 'risk_warning',
                $titleCol => $title,
                $bodyCol  => 'Sistem memprediksi Anda berpotensi menunggak. Yuk bayar lebih awal agar aman dan terhindar dari keterlambatan.',
            ];

            if ($jsonCol) {
                $payload[$jsonCol] = [
                    'period' => $periodKey,
                    'risk'   => (float) $r->risk,
                ];
            }

            AppNotification::query()->create($payload);
            $created++;
        }

        $this->info("OK notify: {$created} notifikasi dibuat untuk period {$periodKey}");
        return self::SUCCESS;
    }
}
