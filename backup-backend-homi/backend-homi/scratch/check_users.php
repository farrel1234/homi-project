<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$manager = app(\App\Support\Tenancy\TenantManager::class);
$tenant = \App\Models\Tenant::where('code', 'hawaii-garden')->first();

echo "=== Tenant Info ===\n";
echo "Name: {$tenant->name}\n";
echo "DB: {$tenant->db_database}\n\n";

$manager->activate($tenant);

echo "=== Users in Hawaii Garden DB ===\n";
$users = \App\Models\User::all(['id','email','name','role','is_active']);
foreach ($users as $u) {
    echo "ID:{$u->id} | {$u->email} | {$u->name} | role:{$u->role} | active:{$u->is_active}\n";
}
