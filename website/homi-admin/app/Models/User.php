<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'role_id',
        'username',
        'email',
        'password_hash',
        'full_name',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Supaya Auth::attempt() pakai kolom password_hash,
     * bukan "password" default Laravel.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Cek apakah user adalah admin (role_id = 1).
     */
    public function isAdmin(): bool
    {
        return (int) $this->role_id === 1;
    }

    /* =======================================================
     |  RELATIONSHIPS
     =======================================================*/

    /**
     * Relasi ke tabel residents (1 user = 1 resident)
     */
    public function resident()
    {
        return $this->hasOne(Resident::class);
    }

    /**
     * Relasi payment (1 user banyak pembayaran)
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relasi permohonan layanan (service_requests)
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Relasi pengaduan warga (complaints)
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
