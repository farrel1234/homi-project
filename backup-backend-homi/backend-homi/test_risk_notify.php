<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\Tenant;
use App\Models\PaymentRiskScore;
use App\Models\User;
use App\Support\Tenancy\TenantManager;
use Illuminate\Support\Carbon;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$manager = app(TenantManager::class);
$tenant = Tenant::where('code', 'hawaii-garden')->first();

if (!$tenant) {
    echo "Tenant Hawaii not found.\n";
    exit;
}

$manager->activate($tenant);

echo "Active DB: " . config('database.connections.mysql.database') . "\n";

// Find a resident in this tenant
$user = User::where('role', 'resident')->first();
if (!$user) {
    echo "No resident found in this tenant. Creating one...\n";
    $user = User::create([
        'name' => 'Tester Risk',
        'email' => 'test-risk@email.com',
        'password' => bcrypt('password'),
        'role' => 'resident',
        'is_active' => 1
    ]);
}

echo "Testing for User: {$user->email}\n";

// Create a high risk score for the current period
$period = now()->startOfMonth()->toDateString();
PaymentRiskScore::query()->updateOrCreate(
    ['user_id' => $user->id, 'period' => $period],
    [
        'risk' => 0.85,
        'predicted_delinquent' => 1,
        'notified_at' => null,
        'computed_at' => now()
    ]
);

echo "Mock Risk Score created. Running command...\n";

Artisan::call('homi:notify-delinquency-risk');
echo Artisan::output();

$rs = PaymentRiskScore::where('user_id', $user->id)->where('period', $period)->first();
echo "Notified At: " . ($rs->notified_at ?? 'STILL NULL') . "\n";
