<?php

namespace App\Console\Commands;

use App\Services\DelinquencyNaiveBayes;
use Illuminate\Console\Command;

class TrainDelinquencyNb extends Command
{
    protected $signature = 'ml:train-delinquency-nb {--months=12} {--grace=7}';
    protected $description = 'Train Naive Bayes model untuk prediksi menunggak iuran';

    public function handle(DelinquencyNaiveBayes $svc): int
    {
        $months = (int)$this->option('months');
        $grace  = (int)$this->option('grace');

        $model = $svc->train($months, $grace);

        $this->info('OK trained: '.$model['name']);
        $this->line('trained_at: '.$model['trained_at']);
        $this->line('grace_days: '.$model['grace_days']);
        $this->line('priors: '.json_encode($model['priors']));

        return self::SUCCESS;
    }
}
