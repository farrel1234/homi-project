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
        
        // Ambil nama database pusat dari config central, fallback ke env jika tidak ada
        $centralDB = config('database.connections.central.database', env('DB_DATABASE', 'homi'));
        
        config(["database.connections.{$connectionName}.database" => $centralDB]);

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
