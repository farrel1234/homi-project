<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlNbModel extends Model
{
    protected $table = 'ml_nb_models';

    protected $fillable = [
        'name',
        'model_json',
        'metrics_json',
        'trained_at',
        // kalau tabel kamu pakai nama kolom lain, tetap aman
        'model',
        'payload',
    ];

    protected $casts = [
        'model_json'   => 'array',
        'metrics_json' => 'array',
        'trained_at'   => 'datetime',
    ];

    /**
     * Ambil payload model dengan fallback ke beberapa nama kolom,
     * dan decode kalau bentuknya string JSON.
     */
    public function getModelPayloadAttribute(): array
    {
        $raw = $this->model_json
            ?? ($this->attributes['model'] ?? null)
            ?? ($this->attributes['payload'] ?? null)
            ?? null;

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }
}
