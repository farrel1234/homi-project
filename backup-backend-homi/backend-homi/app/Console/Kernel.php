<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // score tiap tgl 1 jam 07:00
        $schedule->command('ml:score-delinquency-nb --months=12 --grace=7 --threshold=0.60')
            ->monthlyOn(1, '07:00')
            ->withoutOverlapping();

        // kirim notif risk tiap tgl 1 jam 07:05
        $schedule->command('ml:notify-risk --threshold=0.60')
            ->monthlyOn(1, '07:05')
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
