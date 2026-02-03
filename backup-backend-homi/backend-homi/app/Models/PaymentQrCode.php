<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentQrCode extends Model
{
    protected $table = 'payment_qr_codes';

    protected $fillable = [
        'image_path',
        'is_active',
        'updated_by',
        'notes',
        // kalau suatu saat kamu tambah qr_url/url, aman juga
        'qr_url',
        'url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* ========= Scopes ========= */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /* ========= Accessors ========= */
    public function getDisplayUrlAttribute(): ?string
    {
        // 1) kalau ada kolom url langsung (opsional)
        $qrUrl = $this->qr_url ?? $this->url ?? null;
        if ($qrUrl) return $qrUrl;

        // 2) path file
        $path = $this->image_path
            ?? $this->qr_image_path
            ?? $this->qr_path
            ?? $this->path
            ?? null;

        if (!$path) return null;

        // kalau sudah URL http(s)
        if (preg_match('/^https?:\/\//i', $path)) return $path;

        // normalisasi path
        $p = str_replace('\\', '/', (string) $path);
        $p = ltrim($p, '/');
        $p = preg_replace('#^storage/#', '', $p);
        $p = preg_replace('#^public/#', '', $p);

        // default: file di disk public (storage/app/public)
        return Storage::disk('public')->url($p);
    }
}
