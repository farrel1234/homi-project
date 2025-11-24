<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResidentsSeeder extends Seeder
{
    public function run(): void
    {
        // Cek tabel ada
        if (!Schema::hasTable('residents') || !Schema::hasTable('users')) return;

        // Ambil daftar kolom sebenarnya di tabel residents
        $cols = collect(Schema::getColumnListing('residents'))
            ->map(fn($c) => strtolower($c))
            ->flip();

        // Ambil user warga target
        $users = DB::table('users')
            ->whereIn('email', ['warga1@homi.test','warga2@homi.test'])
            ->get(['id','email','full_name','username'])
            ->keyBy('email');

        $rows = [
            [
                'email'        => 'warga1@homi.test',
                'house_number' => 'A-12',
                'address'      => 'Jl. Hawai Garden Blok A No.12',
                'id_number'    => 'ID-0001',
                'family_head'  => 'Budi',
                'other_info'   => 'KK 4 orang',
            ],
            [
                'email'        => 'warga2@homi.test',
                'house_number' => 'B-07',
                'address'      => 'Jl. Hawai Garden Blok B No.07',
                'id_number'    => 'ID-0002',
                'family_head'  => 'Sari',
                'other_info'   => 'KK 3 orang',
            ],
        ];

        foreach ($rows as $r) {
            $u = $users[$r['email']] ?? null;
            if (!$u) continue;

            // Build payload sesuai kolom yang ADA
            $payload = [
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($cols->has('user_id'))      $payload['user_id'] = $u->id;
            if ($cols->has('house_number')) $payload['house_number'] = $r['house_number'];
            if ($cols->has('address'))      $payload['address'] = $r['address'];
            if ($cols->has('id_number'))    $payload['id_number'] = $r['id_number'];
            if ($cols->has('family_head'))  $payload['family_head'] = $r['family_head'];
            if ($cols->has('other_info'))   $payload['other_info'] = $r['other_info'];

            // Kolom 'name' (NOT NULL) → isi dari family_head | full_name | username
            if ($cols->has('name')) {
                $payload['name'] = $r['family_head'] ?: ($u->full_name ?: $u->username);
            }

            // Tentukan "kunci unik" untuk upsert:
            // - Prioritas pakai user_id jika ada
            // - Kalau tidak, fallback ke id_number (kalau ada)
            if ($cols->has('user_id')) {
                DB::table('residents')->updateOrInsert(
                    ['user_id' => $u->id],
                    $payload
                );
            } elseif ($cols->has('id_number')) {
                DB::table('residents')->updateOrInsert(
                    ['id_number' => $r['id_number']],
                    $payload
                );
            } else {
                // Fallback terakhir: insert biasa
                DB::table('residents')->insert($payload);
            }
        }
    }
}
