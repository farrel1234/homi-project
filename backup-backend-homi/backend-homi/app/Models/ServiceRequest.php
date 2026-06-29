<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';

    protected $fillable = [
        'user_id',
        'assigned_to',
        'request_type_id',
        'reporter_name',
        'request_date',
        'place',
        'subject',
        'data_input',
        'title',
        'description',
        'category',
        'status',
        'status_web',
        'admin_note',
        'rt_name',
        'rt_number',
        'rw_number',
        'verified_by',
        'verified_at',
        'pdf_path',
        'closed_at',
    ];

    protected $casts = [
        'request_date' => 'date',
        'verified_at'  => 'datetime',
        'closed_at'    => 'datetime',
        'data_input'   => 'array', // << penting walau kolomnya longtext
    ];

    protected $appends = ['pdf_url'];

    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->pdf_path) return null;
        return asset('storage/' . ltrim($this->pdf_path, '/'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(RequestType::class, 'request_type_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    public function getStatusLabelAttribute(): string
{
    return match ($this->status) {
        'submitted' => 'Diajukan',
        'processed' => 'Diproses',
        'approved'  => 'Disetujui',
        'rejected'  => 'Ditolak',
        default     => (string) $this->status,
    };
}

public function getStatusBadgeClassAttribute(): string
{
    return match ($this->status) {
        'submitted' => 'bg-slate-100 text-slate-700 border border-slate-200',
        'processed' => 'bg-amber-50 text-amber-700 border border-amber-200',
        'approved'  => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'rejected'  => 'bg-rose-50 text-rose-700 border border-rose-200',
        default     => 'bg-gray-100 text-gray-700 border border-gray-200',
    };
}

    /**
     * Render template_html dengan data_input.
     * Support placeholder {{key}} / {{ key }} dan key snake_case maupun camelCase.
     */
    public function renderHtml(): string
    {
        $tpl = (string) optional($this->type?->letterType)->template_html;
        if ($tpl === '') return '';

        $raw = (array) ($this->data_input ?? []);
        $data = [];

        // buat alias key: original, camel, snake -> semuanya menunjuk value yang sama
        foreach ($raw as $k => $v) {
            $key = trim((string) $k);

            $val = is_scalar($v)
                ? (string) $v
                : json_encode($v, JSON_UNESCAPED_UNICODE);

            foreach (array_unique([$key, \Illuminate\Support\Str::camel($key), \Illuminate\Support\Str::snake($key)]) as $alias) {
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
