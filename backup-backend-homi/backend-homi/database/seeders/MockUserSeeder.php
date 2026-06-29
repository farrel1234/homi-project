<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ResidentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MockUserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@warga.id',
                'house_type' => 'Tipe 36',
                'blok' => 'A1',
                'no_rumah' => '10',
                'rt' => '01',
                'rw' => '05',
                'pekerjaan' => 'Karyawan Swasta'
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@warga.id',
                'house_type' => 'Tipe 45',
                'blok' => 'A1',
                'no_rumah' => '11',
                'rt' => '01',
                'rw' => '05',
                'pekerjaan' => 'PNS'
            ],
            [
                'name' => 'Andi Darmawan',
                'email' => 'andi@warga.id',
                'house_type' => 'Tipe 54',
                'blok' => 'B2',
                'no_rumah' => '05',
                'rt' => '02',
                'rw' => '05',
                'pekerjaan' => 'Wirausaha'
            ],
            [
                'name' => 'Rina Melati',
                'email' => 'rina@warga.id',
                'house_type' => 'Tipe 36',
                'blok' => 'B2',
                'no_rumah' => '06',
                'rt' => '02',
                'rw' => '05',
                'pekerjaan' => 'Ibu Rumah Tangga'
            ],
            [
                'name' => 'Doni Saputra',
                'email' => 'doni@warga.id',
                'house_type' => 'Tipe 70',
                'blok' => 'C3',
                'no_rumah' => '01',
                'rt' => '03',
                'rw' => '05',
                'pekerjaan' => 'Dokter'
            ],
             [
                'name' => 'Lestari',
                'email' => 'lestari@warga.id',
                'house_type' => 'Tipe 45',
                'blok' => 'C3',
                'no_rumah' => '02',
                'rt' => '03',
                'rw' => '05',
                'pekerjaan' => 'Guru'
            ]
        ];

        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('warga123'),
                    'role' => 'resident',
                    'is_verified' => true,
                    'is_active' => true,
                ]
            );

            ResidentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'blok' => $u['blok'],
                    'no_rumah' => $u['no_rumah'],
                    'rt' => $u['rt'],
                    'rw' => $u['rw'],
                    'alamat' => "Blok {$u['blok']} No. {$u['no_rumah']}, RT {$u['rt']}/RW {$u['rw']}",
                    'house_type' => $u['house_type'],
                    'pekerjaan' => $u['pekerjaan'],
                    'jenis_kelamin' => 'Laki-laki', 
                    'tanggal_lahir' => Carbon::now()->subYears(mt_rand(25, 55))->format('Y-m-d'),
                    'tempat_lahir' => 'Jakarta',
                ]
            );
        }
    }
}
