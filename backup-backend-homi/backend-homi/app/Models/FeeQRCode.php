<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FeeQrCode extends Model
{
    // Kalau tabel kamu beda, ubah ini:
    protected $table = 'fee_qr_codes';

    protected $fillable = [
        'label',
        'qr_image_path',   // path di storage/public
        'is_active',
        'notes',
        'activated_at',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'activated_at' => 'datetime',
    ];

    public function getQrUrlAttribute(): ?string
    {
        if (!$this->qr_image_path) return null;
        return Storage::disk('public')->url($this->qr_image_path);
    }
}
