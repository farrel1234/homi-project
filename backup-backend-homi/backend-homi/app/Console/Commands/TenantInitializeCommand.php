<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TenantInitializeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:initialize {code} {--source=hawaii-garden}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize and sync foundational data to a tenant database from a reference source';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $code = $this->argument('code');
        $sourceCode = $this->option('source');
        $manager = app(\App\Support\Tenancy\TenantManager::class);
        
        $tenant = \App\Models\Tenant::where('code', $code)->first();
        if (!$tenant) {
            $this->error("Tenant '$code' not found in central database.");
            return 1;
        }

        $this->info("Initializing Tenant: {$tenant->name}...");

        // 1. Fetch templates from SOURCE (fallback to Hawaii if source is not central)
        $this->info("Fetching templates from source: $sourceCode...");
        
        $sourceTenant = \App\Models\Tenant::where('code', $sourceCode)->first();
        if ($sourceTenant) {
            $manager->activate($sourceTenant);
        } else {
            $manager->deactivate(); // fallback to central
        }

        $letterTypes = \App\Models\LetterType::all();
        $requestTypes = \App\Models\RequestType::all();
        $feeTypes = \App\Models\FeeType::all();
        $qrCodes = \App\Models\PaymentQRCode::all();

        // 2. Activate TARGET Tenant
        $this->info("Switching to Target Tenant DB: {$tenant->db_database}...");
        $manager->activate($tenant);

        // 3. Ensure Migrations
        $this->info("Running migrations...");
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $this->line(\Illuminate\Support\Facades\Artisan::output());

        // 4. Sync Data
        $this->info("Syncing Letter Templates...");
        foreach ($letterTypes as $type) {
            \App\Models\LetterType::updateOrInsert(
                ['name' => $type->name],
                $type->only(['name', 'template_html', 'required_json', 'description'])
            );
        }

        $this->info("Syncing Request Categories...");
        $requestDefaults = [
            'Keamanan' => ['icon' => 'shield-check', 'description' => 'Laporan terkait keamanan lingkungan'],
            'Kebersihan' => ['icon' => 'trash-can', 'description' => 'Laporan terkait sampah dan kebersihan'],
            'Infrastruktur' => ['icon' => 'tools', 'description' => 'Laporan kerusakan jalan, lampu, dll'],
            'Sosial' => ['icon' => 'users', 'description' => 'Kegiatan warga dan hubungan sosial'],
        ];

        foreach ($requestTypes as $type) {
            $defaults = $requestDefaults[$type->name] ?? ['icon' => 'help-circle', 'description' => 'Kategori lainnya'];
            \App\Models\RequestType::updateOrInsert(
                ['name' => $type->name],
                [
                    'name' => $type->name,
                    'description' => $type->description ?? $defaults['description'],
                    'icon' => $type->icon ?? $defaults['icon'],
                    'is_active' => $type->is_active ?? true,
                    'letter_type_id' => $type->letter_type_id
                ]
            );
        }

        $this->info("Syncing Fee Types...");
        $feeDefaults = [
            'Keamanan' => ['amount' => 50000, 'description' => 'Iuran keamanan bulanan'],
            'Kebersihan' => ['amount' => 30000, 'description' => 'Iuran kebersihan lingkungan'],
            'Iuran Sampah' => ['amount' => 25000, 'description' => 'Pengolahan sampah rutin'],
            'Lingkungan' => ['amount' => 30000, 'description' => 'Iuran pemeliharaan lingkungan'],
            'Umum' => ['amount' => 20000, 'description' => 'Iuran fasilitas umum'],
        ];

        foreach ($feeTypes as $type) {
            $matched = null;
            foreach ($feeDefaults as $key => $val) {
                if (stripos($type->name, $key) !== false) {
                    $matched = $val;
                    break;
                }
            }
            $defaults = $matched ?? ['amount' => 0, 'description' => 'Iuran lainnya'];
            
            \App\Models\FeeType::updateOrInsert(
                ['name' => $type->name],
                [
                    'name' => $type->name,
                    'amount' => ($type->amount && $type->amount > 0) ? $type->amount : $defaults['amount'],
                    'is_recurring' => $type->is_recurring ?? true,
                    'description' => $type->description ?? $defaults['description'],
                    'is_active' => $type->is_active ?? true
                ]
            );
        }

        $this->info("Syncing Payment QR...");
        foreach ($qrCodes as $qr) {
            \App\Models\PaymentQRCode::updateOrInsert(
                ['image_path' => $qr->image_path],
                [
                    'image_path' => $qr->image_path,
                    'is_active' => $qr->is_active ?? true,
                    'notes' => $qr->notes ?? ''
                ]
            );
        }

        // Add a default QR if none synced
        if (\App\Models\PaymentQRCode::count() === 0) {
             \App\Models\PaymentQRCode::create([
                'image_path' => 'payment_qr_codes/default_placeholder.jpg',
                'is_active' => true,
                'notes' => 'Placeholder QR - Silakan ganti melalui Dashboard Admin'
            ]);
        }

        $this->info("Syncing Directory (Admin Profile)...");
        // Ensure Admin user and profile
        $adminEmail = "admin.{$code}@homi.id";
        $admin = \App\Models\User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => "Admin {$tenant->name}",
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'is_verified' => true,
                'role' => 'admin'
            ]
        );

        \App\Models\Resident::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'is_public' => true,
                'alamat' => "Blok Admin",
                'rt' => "01",
                'rw' => "01",
                'housing_code' => $code
            ]
        );

        $this->info("Tenant {$tenant->name} initialized and synced successfully.");
        return 0;
    }
}
