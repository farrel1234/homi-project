<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LetterType;

class LetterTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            1 => [
                'name' => 'Surat Pengantar',
                'description' => 'Surat pengantar untuk berbagai keperluan warga ke RT/RW.',
                'template_html' => '<h1>Surat Pengantar</h1><p>Diberikan kepada {{nama}}...</p>',
            ],
            2 => [
                'name' => 'Surat Keterangan Domisili',
                'description' => 'Surat keterangan tempat tinggal warga.',
                'template_html' => '<h1>Surat Keterangan Domisili</h1><p>Menerangkan bahwa {{nama}} tinggal di {{alamat}}...</p>',
            ],
            3 => [
                'name' => 'Surat Keterangan Kematian',
                'description' => 'Surat keterangan pelaporan kematian warga.',
                'template_html' => '<h1>Surat Keterangan Kematian</h1><p>Menerangkan bahwa {{nama}} telah wafat...</p>',
            ],
            4 => [
                'name' => 'Surat Keterangan Usaha',
                'description' => 'Surat keterangan untuk pembukaan atau kepemilikan usaha.',
                'template_html' => '<h1>Surat Keterangan Usaha</h1><p>Menerangkan bahwa {{nama}} memiliki usaha {{namaUsaha}}...</p>',
            ],
            5 => [
                'name' => 'Surat Keterangan Belum Menikah',
                'description' => 'Surat pernyataan status pernikahan warga.',
                'template_html' => '<h1>Surat Keterangan Belum Menikah</h1><p>Menerangkan bahwa {{nama}} belum pernah menikah...</p>',
            ],
        ];

        foreach ($items as $id => $data) {
            LetterType::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'template_html' => $data['template_html']
                ]
            );
        }
    }
}
