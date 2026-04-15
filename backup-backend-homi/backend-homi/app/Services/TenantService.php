<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantService
{
    /**
     * Beralih ke database tenant secara runtime.
     * Mengubah koneksi 'mysql' agar mengarah ke DB tenant.
     */
    public function switchToTenant(Tenant $tenant)
    {
        // HIJACK JALUR UTAMA (Copy logic dari TenantManager agar kompatibel MySQL 8.4)
        $connectionName = config('database.default', 'mysql');

        // Pindahkan target database ke database perumahan ini
        config(["database.connections.{$connectionName}.database" => $tenant->db_database]);

        // Putus koneksi lama & Re-koneksi dengan config baru
        DB::purge($connectionName);
        DB::reconnect($connectionName);
        
        // Simpan metadata tenant saat ini di app container
        app()->instance('currentTenant', $tenant);
    }

    /**
     * Cari tenant berdasarkan ID (Single Connection Strategy)
     */
    public function findById($id)
    {
        // Jika kita di dalam perumahan, kita harus "lompat sejenak" ke DB pusat
        $currentDB = config('database.connections.' . config('database.default', 'mysql') . '.database');
        $isTenantDB = ($currentDB !== 'homi');

        if ($isTenantDB) {
            $manager = app(\App\Support\Tenancy\TenantManager::class);
            $currentTenant = $manager->current();
            
            // Lompat ke pusat
            $manager->deactivate();
            $tenant = Tenant::find($id);
            
            // Lompat balik ke perumahan sebelumnya jika ada
            if ($currentTenant) {
                $manager->activate($currentTenant);
            }
            
            return $tenant;
        }

        return Tenant::find($id);
    }
}
