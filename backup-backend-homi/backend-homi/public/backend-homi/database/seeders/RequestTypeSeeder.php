<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestType;

class RequestTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Surat Pengantar',
            'Perbaikan Fasilitas',
            'Peminjaman Fasilitas',
            'Pengajuan Layanan',
        ];

        foreach ($items as $name) {
            RequestType::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
    