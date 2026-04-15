<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class TenantManager
{
    public function activate(Tenant $tenant): void
    {
        // HIJACK JALUR UTAMA (Default connection)
        $connectionName = config('database.default', 'mysql');

        // Pindahkan target database ke database perumahan ini
        config(["database.connections.{$connectionName}.database" => $tenant->db_database]);

        // Beritahu Laravel untuk me-reset koneksi menggunakan database baru
        DB::purge($connectionName);
        DB::reconnect($connectionName);

        app()->instance('currentTenant', $tenant);
    }

    public function deactivate(): void
    {
        $connectionName = config('database.default', 'mysql');
        
        // Kembalikan ke database pusat (Central / Register utama)
        // Kita gunakan nama 'homi' yang sudah pasti ada di Laragon user
        config(["database.connections.{$connectionName}.database" => 'homi']);

        DB::purge($connectionName);
        DB::reconnect($connectionName);

        app()->forgetInstance('currentTenant');
    }

    public function current(): ?Tenant
    {
        if (! app()->bound('currentTenant')) {
            return null;
        }

        $tenant = app('currentTenant');

        return $tenant instanceof Tenant ? $tenant : null;
    }
}
