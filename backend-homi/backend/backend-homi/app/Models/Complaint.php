<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',              // ← tambah
        'nama_pelapor',
        'tanggal_pengaduan',
        'tempat_kejadian',
        'perihal',
        'foto_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_path) {
            return null;
        }

        return asset('storage/' . $this->foto_path);
    }

}
