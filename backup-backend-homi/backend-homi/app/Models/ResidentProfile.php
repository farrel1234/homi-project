<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResidentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'blok',
        'no_rumah',
        'rt',
        'rw',
        'nama_rt',
        'alamat',
        'pekerjaan',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'house_type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
