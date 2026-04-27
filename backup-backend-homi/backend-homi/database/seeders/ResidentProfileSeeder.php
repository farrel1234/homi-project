<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ResidentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ResidentProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar opsi yang kita inginkan (sesuai standar Naive Bayes)
        $pekerjaanOptions = ["Karyawan Swasta", "PNS / ASN", "Wiraswasta", "Buruh", "Tidak Bekerja", "Lainnya"];
        $houseTypes = ["Tipe 36", "Tipe 45", "Tipe 60", "Tipe 72"];
        $jenisKelamin = ["Laki-laki", "Perempuan"];

        // Ambil semua user dengan role resident
        $residents = User::where('role', 'resident')->get();

        if ($residents->isEmpty()) {
            return;
        }

        foreach ($residents as $user) {
            ResidentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik'           => '123456' . rand(1000000000, 9999999999),
                    'blok'          => collect(['A', 'B', 'C', 'D', 'E'])->random(),
                    'no_rumah'      => rand(1, 100),
                    'rt'            => '0' . rand(1, 9),
                    'rw'            => '0' . rand(1, 5),
                    'nama_rt'       => 'Pak RT ' . collect(['Budi', 'Joko', 'Agus', 'Slamet'])->random(),
                    'alamat'        => 'Jl. Utama No. ' . rand(1, 50),
                    'pekerjaan'     => collect($pekerjaanOptions)->random(),
                    'house_type'    => collect($houseTypes)->random(),
                    'jenis_kelamin' => collect($jenisKelamin)->random(),
                    'tempat_lahir'  => collect(['Jakarta', 'Bandung', 'Semarang', 'Surabaya', 'Medan'])->random(),
                    'tanggal_lahir' => Carbon::now()->subYears(rand(20, 50))->subDays(rand(1, 365)),
                    'is_public'     => true,
                ]
            );
        }
    }
}
