<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    // =========================================================
    //  REGISTER + KIRIM OTP
    // =========================================================
    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email',
            'password'    => 'required|min:6',
            'tenant_code' => 'required|string', // Kode registrasi rahasia
        ]);

        // Verifikasi tenant_code (registration_code) - Case-insensitive & Trimmed
        $tenant = $request->attributes->get('tenant');
        $inputCode = strtoupper(trim((string) $request->tenant_code));
        $registeredCode = strtoupper(trim((string) ($tenant->registration_code ?? '')));

        if (! $tenant || $registeredCode !== $inputCode) {
            \Illuminate\Support\Facades\Log::warning("Registration Failed: Code Mismatch", [
                'input' => $inputCode,
                'expected' => $registeredCode,
                'tenant_found' => (bool)$tenant,
                'tenant_name' => $tenant->name ?? 'N/A'
            ]);
            return $this->errorResponse('Kode registrasi salah. Silakan hubungi pengelola perumahan.', 422);
        }

        $email = strtolower(trim($request->email));
        $name  = trim($request->name);

        $existing = User::where('email', $email)->first();

        $otp = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        if ($existing && ! $existing->is_verified) {
            $existing->name = $name;
            $existing->password = $request->password;
            $existing->otp_code = (string) $otp;
            $existing->otp_purpose = 'register';
            $existing->otp_expires_at = $expiresAt;
            
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'tenant_id')) {
                $existing->tenant_id = $tenant->id;
            }
            
            if ($request->filled('google_id')) {
                $existing->google_id = $request->google_id;
            }
            $existing->save();

            $mailSent = $this->trySendOtp($existing->email, $otp, $existing->name);

            return $this->successResponse(
                data: [
                    'user' => $existing,
                    'otp'  => $otp,
                    'mail_sent' => $mailSent,
                ],
                message: $mailSent
                    ? 'Akun belum terverifikasi. OTP baru sudah dikirim, silakan verifikasi.'
                    : 'OTP dibuat (lihat log). Email gagal terkirim, periksa konfigurasi SMTP.',
                status: 200
            );
        }

        if ($existing && $existing->is_verified) {
            return $this->errorResponse('Email sudah terdaftar dan terverifikasi. Silakan login.', 422);
        }

        $userData = [
            'name'           => $name,
            'email'          => $email,
            'password'       => $request->password,
            'is_verified'    => false,
            'otp_code'       => (string) $otp,
            'otp_purpose'    => 'register',
            'otp_expires_at' => $expiresAt,
        ];
        
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'tenant_id')) {
            $userData['tenant_id'] = $tenant->id;
        }

        if ($request->filled('google_id')) {
            $userData['google_id'] = $request->google_id;
        }

        $user = User::create($userData);

        $mailSent = $this->trySendOtp($user->email, $otp, $user->name);

        return $this->successResponse(
            data: [
                'user' => $user,
                'otp'  => $otp,
                'mail_sent' => $mailSent,
            ],
            message: $mailSent
                ? 'Register berhasil. OTP telah dikirim, silakan verifikasi.'
                : 'Register berhasil. OTP dibuat (lihat log). Email gagal terkirim, periksa SMTP.',
            status: 201
        );
    }

    /**
     * Helper: kirim OTP via email, return false kalau gagal (SMTP belum dikonfigurasi).
     */
    private function trySendOtp(string $email, int $otp, string $name): bool
    {
        try {
            Mail::to($email)->send(new OtpMail($otp, $name));
            return true;
        } catch (\Throwable $e) {
            \Log::warning("[OTP] Gagal kirim email ke {$email}: {$e->getMessage()}");
            \Log::info("[OTP-FALLBACK] Email={$email} OTP={$otp}");
            return false;
        }
    }

    // =========================================================
    //  RESEND OTP (REGISTER)
    // =========================================================
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'tenant_code' => 'required|string', // Pastikan tenant tetap terdeteksi
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) return $this->errorResponse('User tidak ditemukan', 404);
        if ($user->is_verified) return $this->errorResponse('Akun sudah terverifikasi. Silakan login.', 400);

        $otp = random_int(100000, 999999);
        $user->otp_code = (string) $otp;
        $user->otp_purpose = 'register';
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return $this->successResponse(
            data: [
                'email' => $user->email,
                'otp'   => $otp, // DEV only
            ],
            message: 'OTP baru sudah dikirim.'
        );
    }

    // =========================================================
    //  VERIFIKASI OTP (REGISTER)
    // =========================================================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'otp'         => 'required|digits:6',
            'tenant_code' => 'required|string', // Pastikan tenant tetap terdeteksi
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) return $this->errorResponse('User tidak ditemukan', 404);
        if ($user->is_verified) return $this->errorResponse('Akun sudah terverifikasi.', 400);

        if (! $user->otp_code || (string) $user->otp_code !== (string) $request->otp) {
            return $this->errorResponse('Kode OTP salah.', 422);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return $this->errorResponse('Kode OTP sudah kedaluwarsa.', 422);
        }

        if ($user->otp_purpose !== 'register') {
            return $this->errorResponse('OTP ini bukan untuk verifikasi akun.', 422);
        }

        $user->is_verified = true;
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->otp_purpose = null;
        $user->save();

        // Buat profil resident otomatis agar admin bisa melihat di dashboard
        Resident::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'is_public' => true,
            'alamat'    => null,
        ]);

        $tenant = $request->attributes->get('tenant');
        $token = $user->createToken('mobile')->plainTextToken;

        return $this->successResponse(
            data: [
                'user'        => $user,
                'token'       => $token,
                'tenant_name' => $tenant->name ?? 'Homi Garden',
            ],
            message: 'Verifikasi OTP berhasil. Akun sudah aktif.'
        );
    }

    // =========================================================
    //  LOGIN EMAIL/PASSWORD
    // =========================================================
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Email atau password salah', 401);
        }

        if (! $user->is_verified) {
            return $this->errorResponse('Akun belum terverifikasi. Silakan cek OTP Anda.', 403);
        }

        if ($user->otp_purpose === 'reset') {
            $user->otp_code = null;
            $user->otp_purpose = null;
            $user->otp_expires_at = null;
            $user->save();
        }

        $tenant = $request->attributes->get('tenant');
        $token = $user->createToken('mobile')->plainTextToken;

        return $this->successResponse(
            data: [
                'user'        => $user,
                'token'       => $token,
                'tenant_name' => $tenant->name ?? 'Homi Garden',
            ],
            message: 'Login berhasil'
        );
    }

    // =========================================================
    //  LOGIN / REGISTER VIA GOOGLE (ANDROID kirim id_token)
    // =========================================================
    public function loginGoogle(Request $request)
{
    $request->validate([
        'id_token' => 'required|string',
    ]);

    $idToken = $request->id_token;

    // Verifikasi token ke Google
    $resp = Http::get('https://oauth2.googleapis.com/tokeninfo', [
        'id_token' => $idToken,
    ]);

    if (! $resp->ok()) {
        return $this->errorResponse('Token Google tidak valid.', 401);
    }

    $payload = $resp->json();

    // Cek audience (aud) harus WEB client id
    $expectedAud = config('services.google.web_client_id');
    if ($expectedAud && isset($payload['aud']) && $payload['aud'] !== $expectedAud) {
        return $this->errorResponse('Token Google tidak sesuai client app.', 401);
    }

    $googleSub = $payload['sub'] ?? null;
    $email     = isset($payload['email']) ? strtolower(trim($payload['email'])) : null;
    $name      = $payload['name'] ?? 'Warga';

    if (! $googleSub || ! $email) {
        return $this->errorResponse('Data akun Google tidak lengkap.', 422);
    }

    $user = User::where('email', $email)->first();

    // User belum terdaftar -> kirim flag needs_registration
    if (! $user) {
        return response()->json([
            'success' => false,
            'needs_registration' => true,
            'google_email' => $email,
            'google_name'  => $name,
            'google_id'    => $googleSub,
            'message' => 'Akun belum terdaftar. Silakan lengkapi pendaftaran.',
        ], 200);
    }

    // Kalau kamu mau tetap wajib verified (kalau akun hasil daftar email+OTP)
    if (! $user->is_verified) {
        return $this->errorResponse(
            'Akun belum terverifikasi. Silakan verifikasi OTP terlebih dahulu.',
            403
        );
    }

    // Link google_id kalau belum tersimpan
    if (empty($user->google_id)) {
        $user->google_id = $googleSub;
    }

    // Update nama kalau kosong
    if (empty(trim($user->name ?? ''))) {
        $user->name = $name;
    }

    $user->save();

    $token = $user->createToken('mobile')->plainTextToken;

    return $this->successResponse(
        data: [
            'user'  => $user,
            'token' => $token,
        ],
        message: 'Login Google berhasil'
    );
}


    // =========================================================
    //  UPDATE PROFILE (ubah nama)
    // =========================================================
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'      => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:30',
        ]);

        $user = $request->user();

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        
        if ($request->filled('full_name')) {
            $user->full_name = $request->full_name;
            // Update 'name' if still empty or generic
            if (!$user->name || $user->name === 'Warga' || $user->name === $user->email) {
                $user->name = $request->full_name;
            }
        }

        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }

        $user->save();

        return $this->successResponse(
            data: [
                'user' => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'full_name' => $user->full_name,
                    'email'     => $user->email,
                    'phone'     => $user->phone,
                ]
            ],
            message: 'Profil berhasil diperbarui'
        );
    }

    // =========================================================
    //  GANTI PASSWORD
    // =========================================================
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('Password lama salah.', 422);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return $this->errorResponse('Password baru tidak boleh sama dengan password lama.', 422);
        }

        $user->password = $request->new_password;
        $user->save();

        $user->tokens()
            ->where('id', '!=', $request->user()->currentAccessToken()->id)
            ->delete();

        return $this->successResponse(
            data: null,
            message: 'Password berhasil diubah.'
        );
    }

    // =========================================================
    //  LUPA PASSWORD
    // =========================================================
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) return $this->errorResponse('Email tidak terdaftar', 404);

        if (! $user->is_verified) {
            return $this->errorResponse(
                'Akun belum terverifikasi. Silakan verifikasi akun terlebih dahulu.',
                422
            );
        }

        $otp = random_int(100000, 999999);

        $user->otp_code       = (string) $otp;
        $user->otp_purpose    = 'reset';
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return $this->successResponse(
            data: ['email' => $user->email],
            message: 'OTP reset password dikirim ke email.'
        );
    }

    // =========================================================
    //  VERIFY OTP RESET PASSWORD
    // =========================================================
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) return $this->errorResponse('Email tidak terdaftar', 404);
        if ($user->otp_purpose !== 'reset') return $this->errorResponse('OTP ini bukan untuk reset password.', 422);

        if (! $user->otp_code || (string) $user->otp_code !== (string) $request->otp) {
            return $this->errorResponse('OTP salah.', 422);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return $this->errorResponse('OTP sudah kedaluwarsa.', 422);
        }

        $plainResetToken = Str::random(64);
        $user->reset_token = hash('sha256', $plainResetToken);
        $user->reset_token_expires_at = now()->addMinutes(10);
        $user->save();

        return $this->successResponse(
            data: [
                'email'       => $user->email,
                'reset_token' => $plainResetToken,
                'expires_at'  => $user->reset_token_expires_at,
            ],
            message: 'OTP valid, silakan buat password baru.'
        );
    }

    // =========================================================
    //  RESET PASSWORD
    // =========================================================
    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',
            'password'    => 'required|min:6|confirmed',
        ]);

        $hashed = hash('sha256', $request->reset_token);
        $user = User::where('reset_token', $hashed)->first();

        if (! $user) return $this->errorResponse('Reset token tidak valid', 422);
        if (! $user->reset_token_expires_at || $user->reset_token_expires_at->isPast()) {
            return $this->errorResponse('Reset token sudah kedaluwarsa', 422);
        }

        $user->password = $request->password;

        $user->otp_code = null;
        $user->otp_purpose = null;
        $user->otp_expires_at = null;

        $user->reset_token = null;
        $user->reset_token_expires_at = null;

        $user->save();

        $user->tokens()->delete();

        return $this->successResponse(
            data: null,
            message: 'Password berhasil direset. Silakan login dengan password baru.'
        );
    }

    // =========================================================
    //  ME
    // =========================================================
    public function me(Request $request)
    {
        return $this->successResponse(
            data: [
                'user' => [
                    'id'        => $request->user()->id,
                    'name'      => $request->user()->name,
                    'full_name' => $request->user()->full_name,
                    'email'     => $request->user()->email,
                    'phone'     => $request->user()->phone,
                ]
            ],
            message: 'Data profil user'
        );
    }

    // =========================================================
    //  UPDATE FCM TOKEN
    // =========================================================
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return $this->successResponse(
            data: null,
            message: 'FCM Token berhasil diperbarui'
        );
    }

    // =========================================================
    //  UPDATE PROFILE PHOTO
    // =========================================================
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048', // max 2MB
        ]);

        $user = $request->user();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path && \Storage::disk('public')->exists($user->profile_photo_path)) {
                \Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();

            return $this->successResponse(
                data: [
                    'profile_photo_url' => asset('storage/' . $path)
                ],
                message: 'Foto profil berhasil diperbarui'
            );
        }

        return $this->errorResponse('Gagal mengunggah foto', 400);
    }

    // =========================================================
    //  LOGOUT
    // =========================================================
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            data: null,
            message: 'Logout berhasil'
        );
    }
}
