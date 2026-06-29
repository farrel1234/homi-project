<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Complaint;
use App\Models\ServiceRequest;
use App\Models\RequestType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MockActivitySeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 'resident')->get();
        if ($users->isEmpty()) return;

        $requestTypes = RequestType::all();

        // 1. Seeding Pengumuman (Sama untuk semua warga pilih salah satu admin sebagai creator)
        $admin = User::where('role', 'superadmin')->orWhere('role', 'admin')->first();
        if ($admin) {
            \App\Models\Announcement::create([
                'title' => 'Gotong Royong Kebersihan Lingkungan',
                'category' => 'Kegiatan',
                'content' => 'Diharapkan seluruh warga dapat berkumpul di lapangan utama untuk kegiatan gotong royong.',
                'published_at' => Carbon::now()->subDays(2),
                'created_by' => $admin->id,
                'is_pinned' => true,
                'is_public' => true
            ]);

            \App\Models\Announcement::create([
                'title' => 'Pemberitahuan Perbaikan Pipa Air',
                'category' => 'Informasi',
                'content' => 'Akan ada perbaikan pipa air pada area blok A mulai jam 09.00 - 12.00 WIB.',
                'published_at' => Carbon::now()->subDays(5),
                'created_by' => $admin->id,
                'is_public' => true
            ]);
        }

        foreach ($users as $index => $user) {
            // 2. Seeding Pengaduan
            if (mt_rand(0, 1)) {
                Complaint::create([
                    'user_id' => $user->id,
                    'nama_pelapor' => $user->name,
                    'tanggal_pengaduan' => Carbon::now()->subDays(mt_rand(1, 30))->format('Y-m-d'),
                    'tempat_kejadian' => 'Fasilitas Umum Blok ' . chr(mt_rand(65, 67)),
                    'perihal' => mt_rand(0, 1) ? 'Lampu PJU Padam' : 'Pohon Tumbang',
                    'status' => collect(['baru', 'diproses', 'selesai'])->random(),
                ]);
            }

            // 3. Seeding Pengajuan Layanan
            if ($requestTypes->isNotEmpty() && mt_rand(0, 1)) {
                $type = $requestTypes->random();
                ServiceRequest::create([
                    'user_id' => $user->id,
                    'request_type_id' => $type->id,
                    'reporter_name' => $user->name,
                    'request_date' => Carbon::now()->subDays(mt_rand(1, 30))->format('Y-m-d'),
                    'place' => 'Rumah Warga',
                    'subject' => 'Pengajuan ' . $type->name,
                    'status' => collect(['submitted', 'processed', 'approved'])->random(),
                    'data_input' => json_encode(['keperluan' => 'Pribadi']),
                ]);
            }
        }
    }
}
