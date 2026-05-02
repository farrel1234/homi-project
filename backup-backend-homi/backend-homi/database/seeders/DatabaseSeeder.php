<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Deteksi apakah kita sedang di database PUSAT (homi)
        $currentDB = config('database.connections.' . config('database.default', 'mysql') . '.database');
        $isCentral = ($currentDB === 'homi');

        // 1. Selalu buat Super Admin (Akses Global)
        User::updateOrCreate(
            ['email' => 'admin@homi.id'],
            [
                'name' => 'Super Admin Homi',
                'password' => bcrypt('admin123'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        // 2. Buat Tenant Default Jika di DB Pusat (homi)
        if ($isCentral) {
            \App\Models\Tenant::updateOrCreate(
                ['code' => 'hawaii-garden'],
                [
                    'name' => 'Hawaii Garden',
                    'db_host' => config('database.connections.mysql.host', '127.0.0.1'),
                    'db_port' => config('database.connections.mysql.port', '3306'),
                    'db_database' => 'homi_hawaii_db',
                    'db_username' => config('database.connections.mysql.username', 'root'),
                    'db_password' => config('database.connections.mysql.password', ''),
                    'registration_code' => 'HWG123',
                    'is_active' => true,
                    'plan' => 'professional'
                ]
            );

            \App\Models\Tenant::updateOrCreate(
                ['code' => 'taman-lembah-hijau'],
                [
                    'name' => 'Taman Lembah Hijau',
                    'db_host' => config('database.connections.mysql.host', '127.0.0.1'),
                    'db_port' => config('database.connections.mysql.port', '3306'),
                    'db_database' => 'homi_hijau_db',
                    'db_username' => config('database.connections.mysql.username', 'root'),
                    'db_password' => config('database.connections.mysql.password', ''),
                    'registration_code' => 'TLH999',
                    'is_active' => true,
                    'plan' => 'elite'
                ]
            );

            \App\Models\Tenant::updateOrCreate(
                ['code' => 'kawasan-trial'],
                [
                    'name' => 'Kawasan Trial Demo',
                    'db_host' => config('database.connections.mysql.host', '127.0.0.1'),
                    'db_port' => config('database.connections.mysql.port', '3306'),
                    'db_database' => 'homi_trial_db',
                    'db_username' => config('database.connections.mysql.username', 'root'),
                    'db_password' => config('database.connections.mysql.password', ''),
                    'registration_code' => 'TRIAL01',
                    'is_active' => true,
                    'plan' => 'trial',
                    'trial_ends_at' => now()->addDays(14)
                ]
            );
        }

        // 2. Akun Khusus Jika di Database PERUMAHAN
        if (!$isCentral) {
            // Admin Test (Default untuk testing tenant baru)
            User::updateOrCreate(
                ['email' => 'admin@test.id'],
                [
                    'name' => 'Admin Perumahan',
                    'password' => 'password',
                    'role' => 'admin',
                    'is_active' => true,
                ]
            );

            // Admin Asli dari Pendaftar (Jika ada)
            $request = \App\Models\TenantRequest::where('status', 'approved')
                ->where('email', '!=', 'admin@homi.id')
                ->orderByDesc('created_at')
                ->first();

            if ($request) {
                 // Coba cari password dari DB Pusat agar tidak tertimpa 'password' literal
                 $centralUser = User::on('central')->where('email', $request->email)->first();
                 $password = $centralUser ? $centralUser->password : 'password';

                 User::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'name' => $request->manager_name ?? $request->name,
                        'password' => $password,
                        'role' => 'admin',
                        'is_active' => true,
                    ]
                );
            }

            // Jalankan Mock Seeder Khusus Perumahan
            $this->call([
                MockUserSeeder::class,
                MockFinanceSeeder::class,
                MockActivitySeeder::class,
            ]);
        }

        // Seeder pengajuan
        $this->call(LetterTypeSeeder::class);
        $this->call(RequestTypeSeeder::class);

        // Seeder jenis iuran
        $this->call(FeeTypeSeeder::class);

    }

    
}
