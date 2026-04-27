<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Support\Tenancy\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantsMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'homi:tenants-migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan migrasi ke seluruh database Tenant yang terdaftar';

    /**
     * Execute the console command.
     */
    public function handle(TenantManager $manager)
    {
        $this->info("=== Memulai Migrasi Database Tenant ===");

        // 1. Pastikan kembali ke pusat dulu
        $manager->deactivate();

        // 2. Ambil semua tenant
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn("Tidak ada tenant yang ditemukan untuk dimigrasi.");
            return 0;
        }

        $this->info("Ditemukan " . $tenants->count() . " tenant.");

        $successCount = 0;
        $failCount = 0;

        foreach ($tenants as $tenant) {
            $this->comment("--------------------------------------------------");
            $this->comment("Memproses Tenant: {$tenant->name} [{$tenant->db_database}]");

            try {
                // 3. Aktifkan Tenant
                $manager->activate($tenant);

                // 4. Jalankan Migrasi
                Artisan::call('migrate', [
                    '--force' => true,
                ]);

                $output = Artisan::output();
                $this->line($output);

                $this->info("BERHASIL: Migrasi tenant {$tenant->name} selesai.");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("GAGAL: Error pada tenant {$tenant->name}: " . $e->getMessage());
                $failCount++;
            } finally {
                // 5. Selalu balik ke pusat untuk perulangan berikutnya
                $manager->deactivate();
            }
        }

        $this->comment("--------------------------------------------------");
        $this->info("Migrasi Selesai!");
        $this->info("Berhasil: $successCount | Gagal: $failCount");

        return 0;
    }
}
