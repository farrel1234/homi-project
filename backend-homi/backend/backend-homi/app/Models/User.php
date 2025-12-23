<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username','name','full_name','email','phone',
        'password','password_hash',
        'role','role_id','is_active','is_verified',
        'otp_code','otp_purpose','otp_expires_at',
        'reset_token','reset_token_expires_at',
    ];

    protected $hidden = [
        'password','password_hash','remember_token','otp_code',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_verified' => 'boolean',
        'otp_expires_at' => 'datetime',
        'reset_token_expires_at' => 'datetime',
    ];

    /**
     * Laravel Auth akan pakai ini untuk cek password.
     * Kalau `password` kosong tapi `password_hash` ada, tetap bisa login.
     */
    public function getAuthPassword()
    {
        return $this->password ?: $this->password_hash;
    }

    /**
     * Kalau set password, simpan ke dua kolom biar kompatibel.
     */
    public function setPasswordAttribute($value)
    {
        if (!$value) return;

        $hashed = Hash::make($value);
        $this->attributes['password'] = $hashed;
        $this->attributes['password_hash'] = $hashed;
    }

    /**
     * ✅ FIX UTAMA: dipanggil oleh Admin\AuthController
     */
    public function isAdmin(): bool
    {
        // support dua skema sekaligus: role_id atau role string
        return ((int)($this->role_id ?? 0) === 1) || (($this->role ?? '') === 'admin');
    }

    public function residentProfile()
    {
        return $this->hasOne(Resident::class, "user_id");
    }
}
