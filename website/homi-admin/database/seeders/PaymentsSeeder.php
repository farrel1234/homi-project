<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('payments') || !Schema::hasTable('users')) return;

        $cols = collect(Schema::getColumnListing('payments'))
            ->map(fn($c)=>strtolower($c))
            ->flip();

        // Ambil ID user contoh
        $warga = DB::table('users')
            ->whereIn('email', ['warga1@homi.test','warga2@homi.test'])
            ->pluck('id','email');

        $now = Carbon::now();

        $rows = [
            [
                'email'            => 'warga1@homi.test',
                'amount'           => 150000,
                'currency'         => 'IDR',
                'description'      => 'Iuran Bulanan Oktober',
                'due_date'         => $now->copy()->endOfMonth(),
                'paid_at'          => null,
                'status'           => 'pending',
                'payment_method'   => 'transfer',
                'payment_reference'=> 'REF-'.Str::upper(Str::random(6)),
                'created_at'       => $now->copy()->subDays(2),
                'updated_at'       => $now->copy()->subDays(2),
            ],
            [
                'email'            => 'warga2@homi.test',
                'amount'           => 150000,
                'currency'         => 'IDR',
                'description'      => 'Iuran Bulanan Oktober',
                'due_date'         => $now->copy()->endOfMonth(),
                'paid_at'          => $now->copy()->subDay(),
                'status'           => 'paid',
                'payment_method'   => 'transfer',
                'payment_reference'=> 'REF-'.Str::upper(Str::random(6)),
                'created_at'       => $now->copy()->subDays(3),
                'updated_at'       => $now->copy()->subDay(),
            ],
        ];

        foreach ($rows as $r) {
            $userId = $warga[$r['email']] ?? null;
            if (!$userId) continue;

            // build payload sesuai kolom yang ada
            $payload = [];
            if ($cols->has('user_id'))          $payload['user_id'] = $userId;
            if ($cols->has('amount'))           $payload['amount'] = $r['amount'];
            if ($cols->has('currency'))         $payload['currency'] = $r['currency'];
            if ($cols->has('description'))      $payload['description'] = $r['description'];
            if ($cols->has('due_date'))         $payload['due_date'] = $r['due_date'];
            if ($cols->has('paid_at'))          $payload['paid_at'] = $r['paid_at'];
            if ($cols->has('status'))           $payload['status'] = $r['status'];
            if ($cols->has('payment_method'))   $payload['payment_method'] = $r['payment_method'];
            if ($cols->has('payment_reference'))$payload['payment_reference'] = $r['payment_reference'];
            if ($cols->has('created_at'))       $payload['created_at'] = $r['created_at'];
            if ($cols->has('updated_at'))       $payload['updated_at'] = $r['updated_at'];

            // Upsert key:
            // - Jika ada user_id & created_at → cek per user + description + bulan created_at
            $exists = null;
            if ($cols->has('user_id') && $cols->has('created_at')) {
                $exists = DB::table('payments')
                    ->where('user_id', $userId)
                    ->when($cols->has('description'), fn($q)=>$q->where('description', $r['description']))
                    ->whereMonth('created_at', $r['created_at']->month)
                    ->first();
            }

            if ($exists) {
                DB::table('payments')->where('id', $exists->id)->update($payload);
            } else {
                DB::table('payments')->insert($payload);
            }
        }

        // Seed payment_items jika tabelnya ada
        if (Schema::hasTable('payment_items')) {
            $itemCols = collect(Schema::getColumnListing('payment_items'))->map(fn($c)=>strtolower($c))->flip();
            $paymentIds = DB::table('payments')->pluck('id');

            foreach ($paymentIds as $pid) {
                $cnt = DB::table('payment_items')->where('payment_id', $pid)->count();
                if ($cnt == 0) {
                    $set = [];
                    if ($itemCols->has('payment_id') && $itemCols->has('name') && $itemCols->has('amount')) {
                        $set[] = ['payment_id'=>$pid,'name'=>'Iuran Kebersihan','amount'=>100000,'created_at'=>now()];
                        $set[] = ['payment_id'=>$pid,'name'=>'Iuran Keamanan','amount'=> 50000,'created_at'=>now()];
                    }
                    if (!empty($set)) DB::table('payment_items')->insert($set);
                }
            }
        }
    }
}
