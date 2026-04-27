<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FeeType;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FeeInvoiceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $residents = User::where('role', 'resident')->get();
        $feeTypes = FeeType::all();

        if ($residents->isEmpty() || $feeTypes->isEmpty()) {
            return;
        }

        // Kita buat histori untuk 12 bulan terakhir
        $months = 12;

        foreach ($residents as $user) {
            // Tentukan "Tipe Pembayar" warga ini (agar ada pola untuk dipelajari AI)
            // 0: Rajin, 1: Sering Telat, 2: Suka Nunggak
            $behavior = rand(0, 2);

            for ($i = $months; $i >= 0; $i--) {
                $period = Carbon::now()->subMonths($i)->startOfMonth();
                $dueDate = (clone $period)->addDays(10); // Jatuh tempo tanggal 10

                foreach ($feeTypes as $type) {
                    $amount = ($type->name == 'Iuran Sampah') ? 25000 : 50000;

                    // Buat Invoice
                    // Note: updateOrCreate untuk menghindari duplikasi jika seeder dijalankan ulang
                    $invoice = FeeInvoice::updateOrCreate(
                        [
                            'user_id'      => $user->id,
                            'fee_type_id'  => $type->id,
                            'period'       => $period,
                        ],
                        [
                            'amount'       => $amount,
                            'due_date'     => $dueDate,
                            'status'       => 'unpaid',
                        ]
                    );

                    // Tentukan apakah dibayar atau tidak berdasarkan behavior
                    $shouldPay = true;
                    $isLate = false;

                    if ($behavior == 0) { // Rajin
                        $shouldPay = true;
                        $isLate = (rand(1, 100) > 95); // 5% chance telat
                    } elseif ($behavior == 1) { // Sering Telat
                        $shouldPay = (rand(1, 100) > 20); // 80% bayar
                        $isLate = true;
                    } else { // Suka Nunggak
                        $shouldPay = (rand(1, 100) > 60); // Cuma 40% bayar
                        $isLate = (rand(1, 100) > 50);
                    }

                    // Jika dibayar, buat datanya di fee_payments
                    if ($shouldPay) {
                        $paidAt = $isLate 
                            ? (clone $dueDate)->addDays(rand(1, 20)) 
                            : (clone $period)->addDays(rand(1, 9));
                        
                        // Jangan buat pembayaran untuk bulan ini jika belum sampai tanggalnya
                        if ($paidAt->greaterThan(now())) continue;

                        // Cek apakah sudah ada pembayaran
                        if (!$invoice->status == 'paid') {
                           FeePayment::updateOrCreate(
                                ['invoice_id' => $invoice->id],
                                [
                                    'payer_user_id'  => $user->id,
                                    'proof_path'     => 'seeds/dummy_proof.jpg',
                                    'review_status'  => 'approved',
                                    'reviewed_by'    => 1, // Admin (Superadmin)
                                    'reviewed_at'    => $paidAt,
                                    'created_at'     => $paidAt,
                                    'updated_at'     => $paidAt,
                                ]
                            );

                            $invoice->update(['status' => 'paid']);
                        }
                    }
                }
            }
        }
    }
}
