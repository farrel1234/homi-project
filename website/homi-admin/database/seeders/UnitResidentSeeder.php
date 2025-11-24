<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Resident;

class UnitResidentSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 10 unit contoh
        $units = collect(['A1-01','A1-02','A1-03','B2-01','B2-02','C3-01','C3-02','D1-07','D1-08','E2-05']);
        foreach ($units as $code) {
            Unit::firstOrCreate(['code' => $code], []);
        }

        // Buat beberapa warga & mapping ke unit_code (longgar aja)
        $residents = [
            ['name' => 'Andi Warga', 'email' => 'andi@example.com', 'phone' => '081234567890', 'unit_code' => 'D1-07'],
            ['name' => 'Budi',       'email' => 'budi@example.com', 'phone' => '081200011122', 'unit_code' => 'A1-01'],
            ['name' => 'Citra',      'email' => 'citra@example.com','phone' => '081200033344', 'unit_code' => 'A1-02'],
            ['name' => 'Dewi',       'email' => null,               'phone' => '081200055566', 'unit_code' => 'B2-01'],
            ['name' => 'Eko',        'email' => null,               'phone' => null,           'unit_code' => 'E2-05'],
        ];

        foreach ($residents as $r) {
            Resident::firstOrCreate(['name' => $r['name'], 'phone' => $r['phone']], $r);
        }
    }
}
