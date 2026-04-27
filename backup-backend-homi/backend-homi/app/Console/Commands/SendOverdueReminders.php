<?php

namespace App\Console\Commands;

use App\Models\FeeInvoice;
use App\Services\HomiNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendOverdueReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'homi:remind-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat otomatis ke warga yang memiliki tagihan iuran yang telat (overdue)';

    /**
     * Execute the console command.
     */
    public function handle(HomiNotificationService $notifier)
    {
        $this->info("Memulai pengecekan tagihan telat...");

        $now = now();
        
        // Ambil tagihan yang unpaid/pending dan sudah lewat jatuh tempo
        $overdueInvoices = FeeInvoice::with('user')
            ->whereIn('status', ['unpaid', 'pending'])
            ->whereDate('due_date', '<', $now->toDateString())
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->warn("Tidak ada tagihan telat yang ditemukan.");
            return 1;
        }

        $sentCount = 0;
        foreach ($overdueInvoices as $invoice) {
            $user = $invoice->user;
            if (!$user) continue;

            $title = "🚨 Tagihan Iuran Terlambat!";
            $amountFormatted = "Rp " . number_format($invoice->amount, 0, ',', '.');
            $message = "Halo {$user->full_name}, tagihan iuran Anda periode " . $invoice->period->format('M Y') . 
                       " sebesar {$amountFormatted} telah melewati jatuh tempo (" . $invoice->due_date->format('d/m/Y') . 
                       "). Mohon segera lakukan pembayaran melalui aplikasi Homi. Terima kasih.";

            $notifier->notify($user, $title, $message, 'warning', [
                'invoice_id' => $invoice->id,
                'screen' => 'TagihanIuran'
            ]);

            $sentCount++;
            $this->line("Mengirim pengingat ke: {$user->full_name} (ID: {$user->id})");
        }

        $this->info("Selesai! Berhasil mengirim {$sentCount} pengingat.");
        return 0;
    }
}
