<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'id_token' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);

        $payload = $client->verifyIdToken($request->id_token);
        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Google token tidak valid',
                'data' => null
            ], 401);
        }

        $email = $payload['email'] ?? null;
        $name  = $payload['name'] ?? 'Warga';
        $sub   = $payload['sub'] ?? null; // unique google user id

        if (!$email || !$sub) {
            return response()->json([
                'success' => false,
                'message' => 'Data Google tidak lengkap',
                'data' => null
            ], 422);
        }

        // cari user by email, jika belum ada -> create
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                // password random (tidak dipakai saat login google)
                'password' => bcrypt(Str::random(32)),
                // agar tidak perlu OTP lagi
                'email_verified_at' => now(),
            ]);
        } else {
            // kalau sebelumnya user daftar manual tapi belum verify, kita bisa auto-verify
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            if (!$user->name || $user->name === 'Warga') {
                $user->name = $name;
            }
            $user->save();
        }

        // Issue Sanctum token
        $deviceName = $request->device_name ?: 'android';
        $token = $user->createToken($deviceName)->plainTextToken;

        // Cek apakah profil untuk NB + direktori sudah lengkap
        // (sesuaikan kolom/tabel profile kamu)
        // Misal kamu simpan di resident_profiles, kalau belum ada anggap belum lengkap
        $needsProfile = true;
        try {
            // contoh kalau relasi ada:
            // $p = $user->residentProfile;
            // $needsProfile = !$p || empty($p->job) || empty($p->house_type) || empty($p->housing) || empty($p->blok) || empty($p->no_rumah);

            // kalau belum ada struktur jelas, biar aman:
            $needsProfile = true;
        } catch (\Throwable $e) {
            $needsProfile = true;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
                'needs_profile' => $needsProfile,
            ]
        ]);
    }
}
