<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeeType;

class FeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Iuran Sampah',
            'Iuran Keamanan',
            'Iuran Lingkungan',
            'Iuran Fasilitas Umum',
        ];

        foreach ($items as $name) {
            FeeType::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
