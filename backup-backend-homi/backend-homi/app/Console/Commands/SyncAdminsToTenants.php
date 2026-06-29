<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use App\Support\Tenancy\TenantManager;
use Illuminate\Console\Command;

class SyncAdminsToTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'homi:sync-admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi akun Admin dari DB Pusat ke DB Tenant masing-masing';

    /**
     * Execute the console command.
     */
    public function handle(TenantManager $tenantManager, TenantService $tenantService)
    {
        $this->info('Memulai sinkronisasi admin...');

        // 1. Pastikan di pusat
        $tenantManager->deactivate();

        // 2. Ambil semua admin yang punya tenant_id
        $admins = User::where('role', 'admin')->whereNotNull('tenant_id')->get();

        if ($admins->isEmpty()) {
            $this->warn('Tidak ada akun admin yang perlu disinkronkan.');
            return;
        }

        $this->info("Ditemukan {$admins->count()} akun admin.");

        foreach ($admins as $admin) {
            $this->comment("Memproses: {$admin->email} untuk tenant_id: {$admin->tenant_id}");

            $tenant = $tenantService->findById($admin->tenant_id);
            if (!$tenant) {
                $this->error("Gagal: Tenant ID {$admin->tenant_id} tidak ditemukan untuk user {$admin->email}");
                continue;
            }

            try {
                // 3. Switch ke Tenant
                $tenantManager->activate($tenant);

                // 4. Update or Create
                User::updateOrCreate(
                    ['email' => $admin->email],
                    [
                        'name'      => $admin->name,
                        'full_name' => $admin->full_name,
                        'username'  => $admin->username,
                        'password'  => $admin->password, // Copy hash mentah-mentah
                        'role'      => 'admin',
                        'role_id'   => 1,
                        'is_active' => 1,
                        'is_verified' => true,
                        'email_verified_at' => $admin->email_verified_at ?? now(),
                    ]
                );

                $this->info("Berhasil: {$admin->email} sinkron ke DB [{$tenant->db_database}]");

            } catch (\Exception $e) {
                $this->error("Error sinkron {$admin->email}: " . $e->getMessage());
            } finally {
                // 5. Selalu balik ke pusat
                $tenantManager->deactivate();
            }
        }

        $this->info('Sinkronisasi selesai.');
    }
}
