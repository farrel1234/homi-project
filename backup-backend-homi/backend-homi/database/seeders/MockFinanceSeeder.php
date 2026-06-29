<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FeeType;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MockFinanceSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 'resident')->get();
        $feeTypes = FeeType::all();
        
        if ($feeTypes->isEmpty()) {
            $feeTypes = collect([
                FeeType::create(['name' => 'Iuran Keamanan', 'amount' => 50000, 'is_recurring' => true, 'is_active' => true]),
                FeeType::create(['name' => 'Iuran Kebersihan', 'amount' => 30000, 'is_recurring' => true, 'is_active' => true]),
            ]);
        }

        foreach ($users as $index => $user) {
            // Skenario 1 (Index 0 & 1): Warga Baik, selalu bayar tepat waktu
            // Skenario 2 (Index 2): Warga Sering Telat (Telat 20 hari) -> Untuk training Naive Bayes "Late"
            // Skenario 3 (Index 3 & 4): Warga Menunggak (Belum bayar sama sekali 5 bulan terakhir) -> Untuk SAW bobot Tinggi
            // Skenario 4 (Index 5): Menunggak ringan (1 bulan) -> Untuk SAW bobot Rendah

            $monthsBack = 6;
            
            for ($i = $monthsBack; $i > 0; $i--) {
                $periodDate = Carbon::now()->subMonths($i)->startOfMonth();
                $dueDate = $periodDate->copy()->addDays(10); // Jatuh tempo tanggal 10
                
                foreach ($feeTypes as $feeType) {
                    
                    $status = 'unpaid';
                    $paidAt = null;

                    if ($index == 0 || $index == 1) { // LUNAS TEPAT WAKTU
                        $status = 'paid';
                        $paidAt = $periodDate->copy()->addDays(mt_rand(1, 8)); 
                    } elseif ($index == 2) { // LUNAS TELAT
                        $status = 'paid';
                        $paidAt = $dueDate->copy()->addDays(mt_rand(10, 25)); // Bayar telat
                    } elseif ($index == 3 || $index == 4) { // NUNGGAK BERAT
                        // Biarkan unpaid, kecuali bulan paling pertama bayar 
                        if ($i == 6) {
                            $status = 'paid';
                            $paidAt = $periodDate->copy()->addDays(5);
                        } else {
                            $status = 'unpaid';
                        }
                    } elseif ($index == 5) { // NUNGGAK RINGAN (Mulai nunggak bulan ini/kemarin)
                        if ($i > 1) {
                            $status = 'paid';
                            $paidAt = $periodDate->copy()->addDays(5);
                        } else {
                            $status = 'unpaid';
                        }
                    }

                    $invoice = FeeInvoice::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'fee_type_id' => $feeType->id,
                            'period' => $periodDate->format('Y-m-d')
                        ],
                        [
                            'amount' => $feeType->amount,
                            'due_date' => $dueDate->format('Y-m-d'),
                            'status' => $status
                        ]
                    );

                    if ($status === 'paid') {
                        FeePayment::firstOrCreate(
                            [
                                'invoice_id' => $invoice->id,
                            ],
                            [
                                'payer_user_id' => $user->id,
                                'proof_path' => 'dummy/proof.jpg',
                                'review_status' => 'approved',
                                'reviewed_by' => 1,
                                'reviewed_at' => $paidAt->copy()->addHours(2),
                            ]
                        );
                    }
                }
            }
        }
    }
}
