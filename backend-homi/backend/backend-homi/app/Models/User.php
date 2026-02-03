<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\ResidentProfile;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name','email','password',
        'role',
        'is_verified',
        'otp_code','otp_purpose','otp_expires_at',
        'reset_token','reset_token_expires_at',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expires_at'    => 'datetime',
            'is_verified'       => 'boolean',
            'reset_token_expires_at' => 'datetime',
        ];
    }

    public function complaints()
    {
    return $this->hasMany(Complaint::class);
    }

    public function isAdmin(): bool
    {
    return $this->role === 'admin';
    }

    public function residentProfile()
    {
    return $this->hasOne(ResidentProfile::class);
    }

}

