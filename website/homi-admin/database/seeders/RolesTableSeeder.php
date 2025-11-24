<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('roles')) {
            // kalau migrasi belum jalan, skip agar tidak error
            return;
        }

        $rows = [
            ['name' => 'admin', 'description' => 'Administrator', 'created_at' => now()],
            ['name' => 'staff', 'description' => 'Petugas',       'created_at' => now()],
            ['name' => 'warga', 'description' => 'Warga',         'created_at' => now()],
        ];

        foreach ($rows as $r) {
            DB::table('roles')->updateOrInsert(['name' => $r['name']], $r);
        }
    }
}
