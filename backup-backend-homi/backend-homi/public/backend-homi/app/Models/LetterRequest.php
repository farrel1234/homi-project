<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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

    // dipakai view: $item->type->name, dan controller: $letterRequest->type
    public function type()
    {
        return $this->belongsTo(LetterType::class, 'type_id');
    }

    /**
     * Dipakai view index: $item->status_label
     */
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

    /**
     * Dipakai view index: $item->status_badge_class
     */
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
     * Dipakai Admin\LetterRequestController:
     * $letterRequest->renderHtml()
     *
     * template_html: <p>Nama: {{nama}}</p>
     * data_input: {"nama":"Budi", ...}
     */
    public function renderHtml(): string
    {
        $tpl = (string) optional($this->type)->template_html;
        if ($tpl === '') return '';

        $data = (array) ($this->data_input ?? []);

        // replace {{key}} / {{ key }}
        foreach ($data as $k => $v) {
            $key = (string) $k;
            $val = is_scalar($v) ? (string) $v : json_encode($v);

            $tpl = str_replace('{{'.$key.'}}', e($val), $tpl);
            $tpl = str_replace('{{ '.$key.' }}', e($val), $tpl);
        }

        return $tpl;
    }
}
