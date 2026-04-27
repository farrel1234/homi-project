<?php

use Illuminate\Support\Facades\DB;
use Database\Seeders\LetterTypeSeeder;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function syncTemplates($dbName) {
    echo "Processing Database: $dbName...\n";
    try {
        // Configure temporary connection
        config(['database.connections.temp_sync' => array_merge(config('database.connections.mysql'), [
            'database' => $dbName,
        ])]);

        DB::purge('temp_sync');
        DB::setDefaultConnection('temp_sync');

        $seeder = new LetterTypeSeeder();
        $seeder->run();
        
        echo "✅ Successfully updated Letter Templates in $dbName\n";
    } catch (\Exception $e) {
        echo "❌ Error processing $dbName: " . $e->getMessage() . "\n";
    }
}

// 1. Sync Central
syncTemplates('homi');

// 2. Sync Hawaii
syncTemplates('homi_hawaii_db');

echo "\nDone!\n";
