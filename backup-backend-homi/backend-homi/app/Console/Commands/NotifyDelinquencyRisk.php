<?php

namespace App\Console\Commands;

use App\Models\PaymentRiskScore;
use App\Models\User;
use App\Services\HomiNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NotifyDelinquencyRisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'homi:notify-delinquency-risk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi otomatis ke warga yang diprediksi menunggak bulan depan';

    /**
     * Execute the console command.
     */
    public function handle(HomiNotificationService $notifier)
    {
        $this->info("=== Memulai Pengiriman Notifikasi Prediksi Menunggak ===");

        // Cari skor risiko yang prediksinya delinquent (1) dan belum pernah dinotifikasi
        // Untuk periode bulan sekarang atau yang akan datang
        $riskScores = PaymentRiskScore::where('predicted_delinquent', 1)
            ->whereNull('notified_at')
            ->whereDate('period', '>=', now()->startOfMonth()->toDateString())
            ->with('user')
            ->get();

        if ($riskScores->isEmpty()) {
            $this->info("Tidak ada warga berisiko baru yang perlu dinotifikasi.");
            return 0;
        }

        $this->info("Ditemukan " . $riskScores->count() . " warga berisiko.");

        $sentCount = 0;

        foreach ($riskScores as $rs) {
            $user = $rs->user;
            if (!$user) continue;

            $this->comment("Mengirim notifikasi ke: {$user->email}");

            try {
                $periodLabel = Carbon::parse($rs->period)->format('M Y');
                
                $title = "🔔 Pengingat Tertib Iuran";
                $message = "Halo " . ($user->full_name ?? $user->name) . ", sistem kami memantau tagihan iuran Anda untuk periode {$periodLabel}. " .
                           "Mohon pastikan pembayaran tetap tepat waktu untuk menjaga kelancaran akses layanan pengajuan surat di aplikasi Homi. Terima kasih.";

                $notifier->notify($user, $title, $message, 'info', [
                    'screen' => 'TagihanIuran',
                    'type' => 'risk_warning'
                ]);

                // Update agar tidak dikirim lagi untuk periode yang sama
                $rs->update(['notified_at' => now()]);
                $sentCount++;

            } catch (\Exception $e) {
                $this->error("Gagal mengirim ke {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Selesai! Berhasil mengirim {$sentCount} notifikasi.");
        return 0;
    }
}
