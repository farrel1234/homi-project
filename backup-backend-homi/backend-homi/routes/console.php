<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ml:score-delinquency-nb --months=12 --grace=7 --threshold=0.90')
    ->monthlyOn(1, '07:00');

Schedule::command('ml:notify-risk --threshold=0.90')
    ->monthlyOn(1, '07:05');