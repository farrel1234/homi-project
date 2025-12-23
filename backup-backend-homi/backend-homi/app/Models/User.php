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

    /**
     * Kolom yang boleh diisi mass-assignment.
     * Gabungan dari versi temen + versi kamu.
     */
    protected $fillable = [
        // basic identity
        'username',
        'name',
        'full_name',
        'email',
        'phone',

        // auth
        'password',
        'password_hash',

        // roles
        'role',
        'role_id',
        'is_active',
        'is_verified',

        // otp & reset
        'otp_code',
        'otp_purpose',
        'otp_expires_at',
        'reset_token',
        'reset_token_expires_at',
    ];

    /**
     * Kolom yang disembunyikan saat serialize.
     */
    protected $hidden = [
        'password',
        'password_hash',
        'remember_token',
        'otp_code',
    ];

    /**
     * Cast tipe data.
     * (Laravel 12 boleh pakai $casts biasa; ini kompatibel.)
     */
    protected $casts = [
        'email_verified_at' => 'datetime',     // dari versi temen (kalau kolomnya ada, aman)
        'is_active'         => 'boolean',
        'is_verified'       => 'boolean',
        'otp_expires_at'    => 'datetime',
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
     * Relasi: user punya banyak complaints (punya temen).
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Admin checker (gabungan):
     * - Support `role_id` (mis. 1 = admin)
     * - Support `role` string ('admin')
     */
    public function isAdmin(): bool
    {
        return ((int)($this->role_id ?? 0) === 1) || (($this->role ?? '') === 'admin');
    }

    /**
     * Relasi profil resident.
     * - Kalau project temen pakai ResidentProfile, gunakan itu
     * - Kalau project kamu pakai Resident, gunakan itu
     */
    public function residentProfile()
    {
        // Jangan import class yang mungkin tidak ada; pakai FQCN string biar aman.
        if (class_exists(\App\Models\ResidentProfile::class)) {
            return $this->hasOne(\App\Models\ResidentProfile::class);
        }

        // fallback ke skema kamu: Resident dengan foreign key user_id
        return $this->hasOne(\App\Models\Resident::class, 'user_id');
    }
}
