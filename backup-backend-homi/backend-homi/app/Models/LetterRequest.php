<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LetterRequest extends Model
{
    protected $table = 'letter_requests';

    protected $fillable = [
        'user_id',
        'type_id',
        'status',
        'data_input',
        'notes',
        'approved_at',
        'rejected_at',
        'pdf_path',
    ];

    protected $casts = [
        'data_input'   => 'array',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function type()
    {
        return $this->belongsTo(LetterType::class, 'type_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted'  => 'Diajukan',
            'processing' => 'Diproses',
            'approved'   => 'Disetujui',
            'rejected'   => 'Ditolak',
            default      => strtoupper((string) $this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'submitted'  => 'bg-amber-100 text-amber-800',
            'processing' => 'bg-sky-100 text-sky-800',
            'approved'   => 'bg-emerald-100 text-emerald-800',
            'rejected'   => 'bg-rose-100 text-rose-800',
            default      => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Render template_html dengan data_input.
     * Support placeholder {{key}} / {{ key }} dan key snake_case maupun camelCase.
     */
    public function renderHtml(): string
    {
        $tpl = (string) optional($this->type)->template_html;
        if ($tpl === '') return '';

        $raw = (array) ($this->data_input ?? []);
        $data = [];

        // buat alias key: original, camel, snake -> semuanya menunjuk value yang sama
        foreach ($raw as $k => $v) {
            $key = trim((string) $k);

            $val = is_scalar($v)
                ? (string) $v
                : json_encode($v, JSON_UNESCAPED_UNICODE);

            foreach (array_unique([$key, Str::camel($key), Str::snake($key)]) as $alias) {
                if ($alias !== '') $data[$alias] = $val;
            }
        }

        // replace semua {{ ... }}
        $out = preg_replace_callback('/{{\s*([a-zA-Z0-9_]+)\s*}}/', function ($m) use ($data) {
            $k = $m[1];

            // kalau ga ketemu, biarin tetap (biar gampang debug)
            if (!array_key_exists($k, $data)) return $m[0];

            return e($data[$k]);
        }, $tpl);

        return $out ?? $tpl;
    }
}
