<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'nama_pelapor',
        'tanggal_pengaduan',
        'tempat_kejadian',
        'perihal',
        'foto_path',
        'status',        // enum bebas: baru/diproses/selesai (atau sesuai yang kalian pakai)
        'resolved_at',
        'assigned_to',
    ];

    protected $casts = [
        'tanggal_pengaduan' => 'date',
        'resolved_at'       => 'datetime',
    ];

    protected $appends = ['foto_url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // accessor: $complaint->foto_url
    public function getFotoUrlAttribute(): ?string
    {
        $path = $this->foto_path;
        if (! $path) return null;

        // kalau sudah URL penuh
        if (preg_match('/^https?:\\/\\//i', $path)) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
