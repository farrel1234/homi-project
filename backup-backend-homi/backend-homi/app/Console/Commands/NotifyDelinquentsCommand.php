<?php

namespace App\Console\Commands;

use App\Models\FeeInvoice;
use App\Models\User;
use App\Services\DelinquencyNaiveBayes;
use App\Services\HomiNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NotifyDelinquentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ml:notify-delinquents {--dry-run : Only show what would be sent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze residents with unpaid invoices using Naive Bayes and send multi-channel notifications to those at risk.';

    /**
     * Execute the console command.
     */
    public function handle(DelinquencyNaiveBayes $nb, HomiNotificationService $notifier): int
    {
        $this->info("Starting Naive Bayes Delinquency Analysis...");

        $unpaidInvoices = FeeInvoice::query()
            ->where('status', 'unpaid')
            ->orderBy('period', 'desc')
            ->get()
            ->groupBy('user_id');

        $count = 0;
        $sent = 0;

        foreach ($unpaidInvoices as $userId => $userInvoices) {
            $user = User::find($userId);
            if (!$user) continue;

            $latestInv = $userInvoices->first();
            $period = Carbon::parse($latestInv->period);

            try {
                $prediction = $nb->predict((int)$userId, $period);
                
                if ($prediction['label'] === 1) {
                    $prob = round($prediction['prob'] * 100);
                    $this->warn("Resident [{$user->name}] identified as HIGH RISK ({$prob}%).");

                    if ($this->option('dry-run')) {
                        $this->info("Dry-run: Notification would be sent to {$user->email}.");
                    } else {
                        $title = "🔔 Peringatan Penting: Iuran Bulanan";
                        $msg = "Halo " . ($user->full_name ?? $user->name) . 
                               ", sistem mendeteksi Anda memiliki risiko keterlambatan pembayaran iuran " . 
                               $period->format('M Y') . ". Mohon segera lakukan pembayaran untuk menghindari denda atau pemutusan layanan.";
                        
                        $notifier->notify($user, $title, $msg, 'risk_warning', [
                            'invoice_id' => $latestInv->id,
                            'period'     => $period->format('M Y'),
                            'ai_score'   => $prediction['prob'],
                            'source'     => 'ai_auto_system'
                        ]);
                        
                        $sent++;
                    }
                }
            } catch (\Throwable $e) {
                $this->error("Error predicting for User {$userId}: " . $e->getMessage());
            }
            
            $count++;
        }

        $this->info("Analysis complete. Processed: {$count}, Notifications Sent: {$sent}.");

        return Command::SUCCESS;
    }
}
