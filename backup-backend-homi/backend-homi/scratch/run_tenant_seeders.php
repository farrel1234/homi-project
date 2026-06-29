<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Running Seeders across Tenants...\n";
$tenants = \App\Models\Tenant::all();

foreach ($tenants as $tenant) {
    echo "\n=== Seeding {$tenant->name} ({$tenant->db_database}) ===\n";
    $originalDb = config('database.connections.mysql.database');
    config(['database.connections.mysql.database' => $tenant->db_database]);
    \Illuminate\Support\Facades\DB::purge('mysql');
    \Illuminate\Support\Facades\DB::reconnect('mysql');
    
    try {
        \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        echo "Done seeding {$tenant->db_database}!\n";
    } catch (\Exception $e) {
        if (str_contains($e->getMessage(), 'Unknown database')) {
             echo "Database {$tenant->db_database} does not exist yet. Please create it first.\n";
        } else {
             echo "Exception: " . $e->getMessage() . "\n";
        }
    }
}

// Reset connection
config(['database.connections.mysql.database' => 'homi']);
\Illuminate\Support\Facades\DB::purge('mysql');
\Illuminate\Support\Facades\DB::reconnect('mysql');
echo "\nAll Tenants Seeded!\n";
