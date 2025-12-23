<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    // == REGISTER + KIRIM OTP ==
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $otp = random_int(100000, 999999);      // 6 digit
        $expiresAt = now()->addMinutes(10);     // OTP berlaku 10 menit

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            // di-hash otomatis oleh casts() di User
            'password'      => $request->password,
            'is_verified'   => false,
            'otp_code'      => (string) $otp,
            'otp_purpose'  => 'register',
            'otp_expires_at'=> $expiresAt,
        ]);
        
        //kirim otp ke email
        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        // NOTE: untuk DEV kita kirim OTP di response.
        return $this->successResponse(
            data: [
                'user' => $user,
                'otp'  => $otp, // hanya untuk testing
            ],
            message: 'Register berhasil. OTP telah dikirim, silakan verifikasi.',
            status: 201
        );
    }

    // == VERIFIKASI OTP ==
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        if ($user->is_verified) {
            return $this->errorResponse('Akun sudah terverifikasi.', 400);
        }

        if (! $user->otp_code || (string)$user->otp_code !== (string)$request->otp) {
            return $this->errorResponse('Kode OTP salah.', 422);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return $this->errorResponse('Kode OTP sudah kedaluwarsa.', 422);
        }

        if ($user->otp_purpose !== 'register') {
            return $this->errorResponse('OTP ini bukan untuk verifikasi akun.', 422);
        }


        // OTP valid → verifikasi akun
        $user->is_verified    = true;
        $user->otp_code       = null;
        $user->otp_expires_at = null;
        $user->otp_purpose = null;
        $user->save();

        // Setelah verifikasi, langsung buat token login
        $token = $user->createToken('mobile')->plainTextToken;

        return $this->successResponse(
            data: [
                'user'  => $user,
                'token' => $token,
            ],
            message: 'Verifikasi OTP berhasil. Akun sudah aktif.'
        );
    }

    // == LOGIN → hanya boleh untuk akun yang sudah terverifikasi ==
   public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Email atau password salah', 401);
        }

        if (! $user->is_verified) {
            return $this->errorResponse(
                'Akun belum terverifikasi. Silakan cek OTP Anda.',
                403
            );
        }

        // ✅ Jika sebelumnya sempat minta "Lupa Password" tapi tidak dilanjutkan,
        // batalkan OTP reset agar tidak bisa dipakai lagi
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


    // == Ubah nama pengguna ==
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user(); // user dari token Sanctum
        $user->name = $request->name;
        $user->save();

        return response()->json([
            'message' => 'Nama berhasil diperbarui',
            'data' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    // == Ganti Password ==
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:6|confirmed',
            // client kirim: new_password_confirmation
        ]);

        $user = $request->user();

        // cek password lama
        if (! Hash::check($request->current_password, $user->password)) {
        return $this->errorResponse('Password lama salah.', 422);
        }

        // cegah password baru sama dengan password lama (opsional tapi bagus)
        if (Hash::check($request->new_password, $user->password)) {
            return $this->errorResponse('Password baru tidak boleh sama dengan password lama.', 422);
        }

        // update password (kalau kamu pakai casts "password" => "hashed", ini otomatis hash)
        $user->password = $request->new_password;
        $user->save();

        // opsi keamanan: hapus semua token lain, biarkan device ini tetap login
        $user->tokens()
            ->where('id', '!=', $request->user()->currentAccessToken()->id)
            ->delete();

        return $this->successResponse(
            data: null,
            message: 'Password berhasil diubah.'
        );
    }


    // == Lupa password ==
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse('Email tidak terdaftar', 404);
        }

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


    // == OTP untuk reset password ==
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse('Email tidak terdaftar', 404);
        }

        if ($user->otp_purpose !== 'reset') {
            return $this->errorResponse('OTP ini bukan untuk reset password.', 422);
        }

        if (! $user->otp_code || (string)$user->otp_code !== (string)$request->otp) {
            return $this->errorResponse('OTP salah.', 422);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return $this->errorResponse('OTP sudah kedaluwarsa.', 422);
        }

        // OTP valid
        $plainResetToken = Str::random(64); // token yang dikirim ke client
        $user->reset_token = hash('sha256', $plainResetToken); // simpan HASH di DB
        $user->reset_token_expires_at = now()->addMinutes(10);
        $user->save();

        return $this->successResponse(
            data: [
                'email' => $user->email,
                'reset_token' => $plainResetToken,
                'expires_at' => $user->reset_token_expires_at,
            ],
            message: 'OTP valid, silakan buat password baru.'
        );

    }


    // == reset password ==
    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token'            => 'required|string',
            'password'               => 'required|min:6|confirmed',
        ]);

        $hashed = hash('sha256', $request->reset_token);

        $user = User::where('reset_token', $hashed)->first();

        if (! $user) {
            return $this->errorResponse('Reset token tidak valid', 422);
        }

        if (! $user->reset_token_expires_at || $user->reset_token_expires_at->isPast()) {
            return $this->errorResponse('Reset token sudah kedaluwarsa', 422);
        }

        // Update password
        $user->password = $request->password; // karena casts hashed sudah aktif, ini otomatis ke-hash
        // kalau kamu tidak pakai casts hashed, ganti: Hash::make($request->password)

        // Bersihkan OTP + reset token
        $user->otp_code = null;
        $user->otp_purpose = null;
        $user->otp_expires_at = null;

        $user->reset_token = null;
        $user->reset_token_expires_at = null;

        $user->save();

        // Hapus token Sanctum (biar aman kalau sebelumnya ada sesi aktif)
        $user->tokens()->delete();

        return $this->successResponse(
            data: null,
            message: 'Password berhasil direset. Silakan login dengan password baru.'
        );
    }




    public function me(Request $request)
    {
        return $this->successResponse(
            data: $request->user(),
            message: 'Data profil user'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            data: null,
            message: 'Logout berhasil'
        );
    }
    
}
