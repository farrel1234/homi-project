<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class TenantUpsert extends Command
{
    protected $signature = 'tenant:upsert
        {code : Kode tenant, contoh: hawaii-garden}
        {name : Nama perumahan}
        {db_database : Nama database tenant}
        {--host=127.0.0.1 : Host database tenant}
        {--port=3306 : Port database tenant}
        {--username=root : Username database tenant}
        {--password= : Password database tenant}
        {--domain= : Domain/subdomain tenant (opsional)}
        {--driver=mysql : Driver database}
        {--inactive : Set tenant nonaktif}';

    protected $description = 'Create or update tenant registry entry';

    public function handle(): int
    {
        $code = trim((string) $this->argument('code'));
        $name = trim((string) $this->argument('name'));
        $database = trim((string) $this->argument('db_database'));

        $tenant = Tenant::updateOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'domain' => $this->nullIfEmpty((string) $this->option('domain')),
                'db_driver' => (string) $this->option('driver'),
                'db_host' => (string) $this->option('host'),
                'db_port' => (int) $this->option('port'),
                'db_database' => $database,
                'db_username' => (string) $this->option('username'),
                'db_password' => (string) $this->option('password'),
                'is_active' => ! (bool) $this->option('inactive'),
            ]
        );

        $this->info('Tenant tersimpan:');
        $this->line(' - ID: '.$tenant->id);
        $this->line(' - Code: '.$tenant->code);
        $this->line(' - Name: '.$tenant->name);
        $this->line(' - DB: '.$tenant->db_host.':'.$tenant->db_port.'/'.$tenant->db_database);
        $this->line(' - Active: '.($tenant->is_active ? 'yes' : 'no'));

        return self::SUCCESS;
    }

    private function nullIfEmpty(string $value): ?string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
