<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class TenantManager
{
    public function activate(Tenant $tenant): void
    {
        config([
            'database.connections.tenant.driver' => $tenant->db_driver ?: 'mysql',
            'database.connections.tenant.host' => $tenant->db_host,
            'database.connections.tenant.port' => $tenant->db_port ?: 3306,
            'database.connections.tenant.database' => $tenant->db_database,
            'database.connections.tenant.username' => $tenant->db_username,
            'database.connections.tenant.password' => $tenant->db_password,
        ]);

        DB::purge('tenant');
        DB::setDefaultConnection('tenant');
        DB::reconnect('tenant');

        app()->instance('currentTenant', $tenant);
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
