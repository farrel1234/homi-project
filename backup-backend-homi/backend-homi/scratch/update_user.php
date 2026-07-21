<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$manager = app(\App\Support\Tenancy\TenantManager::class);
$t = \App\Models\Tenant::where('code', 'hawaii-garden')->first();
$manager->activate($t);

$u = \App\Models\User::where('email', 'budi@warga.id')->first();
if ($u) {
    $u->password = \Illuminate\Support\Facades\Hash::make('password');
    $u->email_verified_at = now();
    $u->save();
    echo "USER_UPDATED_SUCCESSFULLY";
} else {
    echo "USER_NOT_FOUND";
}
