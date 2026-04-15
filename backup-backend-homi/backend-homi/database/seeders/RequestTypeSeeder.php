<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestType;

class RequestTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            1 => ['name' => 'Surat Pengantar', 'letter_type_id' => 1],
            2 => ['name' => 'Perbaikan Fasilitas', 'letter_type_id' => null],
            3 => ['name' => 'Peminjaman Fasilitas', 'letter_type_id' => null],
            4 => ['name' => 'Pengajuan Layanan', 'letter_type_id' => null],
            5 => ['name' => 'Surat Domisili', 'letter_type_id' => 2],
            6 => ['name' => 'Surat Kematian', 'letter_type_id' => 3],
            7 => ['name' => 'Surat Keterangan Usaha', 'letter_type_id' => 4],
            8 => ['name' => 'Surat Belum Menikah', 'letter_type_id' => 5],
        ];

        foreach ($items as $id => $data) {
            RequestType::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $data['name'],
                    'is_active' => true,
                    'letter_type_id' => $data['letter_type_id']
                ]
            );
        }
    }
}
    