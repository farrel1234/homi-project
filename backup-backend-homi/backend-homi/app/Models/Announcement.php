<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    /**
     * Gabungan fillable temen + kamu.
     * Aman walau sebagian kolom belum ada di DB (asalkan controller tidak memaksa isi kolom tsb).
     */
    protected $fillable = [
        'title',
        'category',
        'content',
        'body',
        'image_path',
        'published_at',
        'start_at',
        'end_at',
        'created_by',
        'author_id',
        'is_pinned',
        'is_public',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'start_at'     => 'datetime',
        'end_at'       => 'datetime',
        'is_pinned'    => 'boolean',
        'is_public'    => 'boolean',
    ];

    protected $appends = ['image_url'];

    /**
     * Penulis/pembuat pengumuman.
     * Paling konsisten pakai created_by (sudah dipakai di dua versi).
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * URL gambar pengumuman.
     * - handle kalau image_path sudah mengandung "storage/"
     * - handle leading "/"
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) return null;

        $path = ltrim($this->image_path, '/');

        // kalau sudah "storage/xxx", jangan dobel storage
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return asset('storage/' . $path);
    }
}
