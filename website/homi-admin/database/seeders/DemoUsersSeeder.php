<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan roles ada
        $roles = DB::table('roles')->pluck('id','name');

        $baseUsers = [
            [
                'role_key'   => 'admin',
                'username'   => 'admin',
                'email'      => 'admin@homi.test',
                'plain_pass' => 'admin123',
                'full_name'  => 'Admin HOMI',
                'phone'      => '081234567890',
            ],
            [
                'role_key'   => 'staff',
                'username'   => 'staff1',
                'email'      => 'staff1@homi.test',
                'plain_pass' => 'staff123',
                'full_name'  => 'Staff Validasi',
                'phone'      => '081111111111',
            ],
            [
                'role_key'   => 'warga',
                'username'   => 'warga1',
                'email'      => 'warga1@homi.test',
                'plain_pass' => 'warga123',
                'full_name'  => 'Budi Warga',
                'phone'      => '082222222222',
            ],
            [
                'role_key'   => 'warga',
                'username'   => 'warga2',
                'email'      => 'warga2@homi.test',
                'plain_pass' => 'warga123',
                'full_name'  => 'Sari Warga',
                'phone'      => '083333333333',
            ],
        ];

        // Ambil daftar kolom yg BENAR-BENAR ada di tabel users
        $cols = collect(Schema::getColumnListing('users'))->map(fn($c)=>strtolower($c))->flip();

        foreach ($baseUsers as $b) {
            $row = [
                'email'      => $b['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $hashed = Hash::make($b['plain_pass']);

            // set nilai hanya jika kolomnya memang ada
            if ($cols->has('role_id'))          $row['role_id'] = $roles[$b['role_key']] ?? null;
            if ($cols->has('username'))         $row['username'] = $b['username'];
            if ($cols->has('full_name'))        $row['full_name'] = $b['full_name'];
            if ($cols->has('phone'))            $row['phone'] = $b['phone'];
            if ($cols->has('is_active'))        $row['is_active'] = 1;
            if ($cols->has('password_hash'))    $row['password_hash'] = $hashed;
            if ($cols->has('password'))         $row['password'] = $hashed;     // <-- INI WAJIB kalau kolom NOT NULL
            if ($cols->has('name'))             $row['name'] = $b['full_name'] ?? $b['username'];
            if ($cols->has('email_verified_at'))$row['email_verified_at'] = now();
            if ($cols->has('remember_token'))   $row['remember_token'] = \Str::random(10);

            DB::table('users')->updateOrInsert(['email' => $row['email']], $row);
        }
    }
}
