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
        // Putus koneksi lama jika ada
        DB::purge('mysql');

        // Update konfigurasi koneksi 'mysql' secara dinamis
        Config::set('database.connections.mysql.database', $tenant->db_database);
        Config::set('database.connections.mysql.username', $tenant->db_username);
        Config::set('database.connections.mysql.password', $tenant->db_password);
        
        if ($tenant->db_host) {
            Config::set('database.connections.mysql.host', $tenant->db_host);
        }
        
        if ($tenant->db_port) {
            Config::set('database.connections.mysql.port', $tenant->db_port);
        }

        // Re-koneksi dengan config baru
        DB::reconnect('mysql');
        
        // Simpan metadata tenant saat ini di app container (opsional tapi berguna)
        app()->instance('current_tenant', $tenant);
    }
    
    /**
     * Cari tenant berdasarkan ID (dari central DB)
     */
    public function findById($id)
    {
        return Tenant::on('central')->find($id);
    }
}
