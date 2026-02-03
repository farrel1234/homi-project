<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $email = strtolower(trim($request->email));
        $name  = trim($request->name);

        $existing = User::where('email', $email)->first();

        $otp = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        if ($existing && ! $existing->is_verified) {
            $existing->name = $name;
            $existing->password = $request->password; // casts hashed aktif -> auto hash
            $existing->otp_code = (string) $otp;
            $existing->otp_purpose = 'register';
            $existing->otp_expires_at = $expiresAt;
            $existing->save();

            Mail::to($existing->email)->send(new OtpMail($otp, $existing->name));

            return $this->successResponse(
                data: [
                    'user' => $existing,
                    'otp'  => $otp, // DEV only
                ],
                message: 'Akun belum terverifikasi. OTP baru sudah dikirim, silakan verifikasi.',
                status: 200
            );
        }

        if ($existing && $existing->is_verified) {
            return $this->errorResponse('Email sudah terdaftar dan terverifikasi. Silakan login.', 422);
        }

        $user = User::create([
            'name'           => $name,
            'email'          => $email,
            'password'       => $request->password,
            'is_verified'    => false,
            'otp_code'       => (string) $otp,
            'otp_purpose'    => 'register',
            'otp_expires_at' => $expiresAt,
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return $this->successResponse(
            data: [
                'user' => $user,
                'otp'  => $otp, // DEV only
            ],
            message: 'Register berhasil. OTP telah dikirim, silakan verifikasi.',
            status: 201
        );
    }

    // =========================================================
    //  RESEND OTP (REGISTER)
    // =========================================================
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
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
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
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

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->successResponse(
            data: [
                'user'  => $user,
                'token' => $token,
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

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->successResponse(
            data: [
                'user'  => $user,
                'token' => $token,
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

    // ✅ PENTING: HANYA BOLEH LOGIN kalau email sudah terdaftar di HOMI
    $user = User::where('email', $email)->first();

    if (! $user) {
        // ini “permission”-nya: akun google yg belum didaftarkan admin/tidak terdaftar → ditolak
        return $this->errorResponse(
            'Akun Google ini belum terdaftar sebagai warga. Silakan daftar/konfirmasi ke admin perumahan.',
            403
        );
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
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $user->name = $request->name;
        $user->save();

        return $this->successResponse(
            data: [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ]
            ],
            message: 'Nama berhasil diperbarui'
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
                    'id'    => $request->user()->id,
                    'name'  => $request->user()->name,
                    'email' => $request->user()->email,
                ]
            ],
            message: 'Data profil user'
        );
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
