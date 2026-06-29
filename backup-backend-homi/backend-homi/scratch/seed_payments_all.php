<?php

use App\Models\Tenant;
use App\Models\User;
use App\Models\FeeType;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Support\Tenancy\TenantManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\ResidentProfile;

// Pastikan script dijalankan melalui CLI / Artisan Tinker
if (php_sapi_name() !== 'cli') {
    die('Only CLI access allowed');
}

$manager = app(TenantManager::class);

// Ambil semua tenant dari database central
// Karena default connection di CLI bisa jadi central atau tenant, mari switch ke central dulu
$manager->deactivate();
$tenants = Tenant::all();

echo "Menemukan " . $tenants->count() . " tenant.\n";

foreach ($tenants as $tenant) {
    echo "=========================================\n";
    echo "MEMPROSES TENANT: {$tenant->name} ({$tenant->db_database})\n";
    echo "=========================================\n";

    try {
        // Aktifkan koneksi ke database tenant
        $manager->activate($tenant);

        // 1. Pastikan FeeType ada
        $feeTypes = FeeType::all();
        if ($feeTypes->isEmpty()) {
            $feeTypes = collect([
                FeeType::create(['name' => 'Iuran Keamanan', 'amount' => 50000, 'is_recurring' => true, 'is_active' => true]),
                FeeType::create(['name' => 'Iuran Kebersihan', 'amount' => 30000, 'is_recurring' => true, 'is_active' => true]),
                FeeType::create(['name' => 'Iuran Fasilitas', 'amount' => 100000, 'is_recurring' => true, 'is_active' => true]),
            ]);
            echo "-> Fee types berhasil dibuat.\n";
        } else {
            $feeTypes = $feeTypes->take(3);
        }

        // 2. Pastikan Users (Residents) ada
        $residents = User::where('role', 'resident')->get();
        if ($residents->isEmpty()) {
            // Jalankan seeder warga
            $seeder = new \Database\Seeders\MockUserSeeder();
            $seeder->run();
            $residents = User::where('role', 'resident')->get();
            echo "-> Warga berhasil dibuat.\n";
        }

        if ($residents->isEmpty()) {
            echo "[WARNING] Tidak ada warga ditemukan untuk tenant ini. Melewati...\n";
            continue;
        }

        // Bersihkan data lama pembayaran & invoice agar rapi
        FeePayment::truncate();
        // Karena ada foreign key constraint, kita matikan checks sebentar
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        FeeInvoice::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        echo "-> Data pembayaran & invoice lama dibersihkan.\n";

        // Ambil admin/reviewer (biasanya user id 1 atau superadmin/admin)
        $admin = User::where('role', 'admin')->first() ?? User::where('role', 'superadmin')->first();
        $adminId = $admin ? $admin->id : 1;

        // Loop untuk membuat variasi data pembayaran
        // Kita buat data 3 bulan terakhir
        $months = [
            Carbon::now()->subMonths(2)->startOfMonth(),
            Carbon::now()->subMonths(1)->startOfMonth(),
            Carbon::now()->startOfMonth(),
        ];

        $paymentCount = 0;

        foreach ($residents as $index => $resident) {
            foreach ($months as $mIdx => $period) {
                foreach ($feeTypes as $fIdx => $feeType) {
                    $dueDate = $period->copy()->addDays(10);
                    
                    // Skenario Pembayaran berdasarkan indeks warga & bulan
                    // Warga 0: Semua LUNAS (Disetujui)
                    // Warga 1: Ada Lunas, Ada Pending (Menunggu Persetujuan), Ada Ditolak
                    // Warga 2: Belum Bayar Sama Sekali (Unpaid)
                    // Warga 3: Semua Pending (Menunggu Persetujuan)
                    // Warga lainnya: Campuran

                    $status = 'unpaid';
                    $reviewStatus = null;
                    $hasPayment = false;

                    if ($index == 0) {
                        // Semua Lunas
                        $status = 'paid';
                        $reviewStatus = 'approved';
                        $hasPayment = true;
                    } elseif ($index == 1) {
                        if ($mIdx == 0) {
                            $status = 'paid';
                            $reviewStatus = 'approved';
                            $hasPayment = true;
                        } elseif ($mIdx == 1) {
                            $status = 'pending';
                            $reviewStatus = 'pending';
                            $hasPayment = true;
                        } else {
                            $status = 'rejected';
                            $reviewStatus = 'rejected';
                            $hasPayment = true;
                        }
                    } elseif ($index == 2) {
                        // Belum Bayar (Unpaid) - tidak ada record payment
                        $status = 'unpaid';
                        $hasPayment = false;
                    } elseif ($index == 3) {
                        // Semua Pending
                        $status = 'pending';
                        $reviewStatus = 'pending';
                        $hasPayment = true;
                    } else {
                        // Campuran acak
                        $r = ($index + $mIdx + $fIdx) % 4;
                        if ($r == 0) {
                            $status = 'paid';
                            $reviewStatus = 'approved';
                            $hasPayment = true;
                        } elseif ($r == 1) {
                            $status = 'pending';
                            $reviewStatus = 'pending';
                            $hasPayment = true;
                        } elseif ($r == 2) {
                            $status = 'rejected';
                            $reviewStatus = 'rejected';
                            $hasPayment = true;
                        } else {
                            $status = 'unpaid';
                            $hasPayment = false;
                        }
                    }

                    // Create Invoice
                    $invoice = FeeInvoice::create([
                        'user_id' => $resident->id,
                        'fee_type_id' => $feeType->id,
                        'period' => $period->format('Y-m-d'),
                        'amount' => $feeType->amount,
                        'due_date' => $dueDate->format('Y-m-d'),
                        'status' => $status,
                        'trx_id' => 'TRX-' . strtoupper(Str::random(8)),
                    ]);

                    // Create Payment
                    if ($hasPayment) {
                        FeePayment::create([
                            'invoice_id' => $invoice->id,
                            'payer_user_id' => $resident->id,
                            'proof_path' => 'dummy/proof.jpg',
                            'note' => $reviewStatus == 'approved' ? 'Bukti pembayaran valid.' : ($reviewStatus == 'rejected' ? 'Bukti buram / nominal tidak sesuai.' : 'Mohon dicek min.'),
                            'review_status' => $reviewStatus,
                            'reviewed_by' => $reviewStatus != 'pending' ? $adminId : null,
                            'reviewed_at' => $reviewStatus != 'pending' ? Carbon::now()->subDays(mt_rand(1, 5)) : null,
                        ]);
                        $paymentCount++;
                    }
                }
            }
        }

        echo "-> Berhasil generate {$paymentCount} dummy pembayaran untuk tenant ini.\n";

    } catch (\Exception $e) {
        echo "[ERROR] Terjadi kesalahan pada tenant ini: " . $e->getMessage() . "\n";
    }
}

// Reset koneksi kembali ke central
$manager->deactivate();
echo "=========================================\n";
echo "PROSES SEEDING SELESAI!\n";
echo "=========================================\n";
