<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterRequest extends Model
{
    protected $fillable = [
        'user_id',
        'letter_type_id',
        'status',
        'data_input',
        'pdf_path',
    ];

    protected $casts = [
        'data_input' => 'array',
    ];

    public function type()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Label status untuk UI
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted'  => 'Diajukan',
            'processed'  => 'Diproses',
            'approved'   => 'Disetujui',
            'rejected'   => 'Ditolak',
            default      => '-',
        };
    }

    // Badge style
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'submitted'  => 'bg-gray-100 text-gray-700 border border-gray-200',
            'processed'  => 'bg-sky-50 text-sky-700 border border-sky-200',
            'approved'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'rejected'   => 'bg-rose-50 text-rose-700 border border-rose-200',
            default      => 'bg-gray-50 text-gray-600 border border-gray-200',
        };
    }

    // Helper untuk build variable ke template
    public function buildTemplateVariables(): array
    {
        $data = $this->data_input ?? [];

        // Info dasar warga
        $user = $this->user;
        $dataDefault = [
            'nama'       => $user->full_name ?? $user->username ?? '',
            'email'      => $user->email ?? '',
            'tanggal'    => now()->translatedFormat('d F Y'),
            'alamat'     => $user->resident->address ?? '' ?? ($data['alamat'] ?? ''), // kalau nanti ada relasi resident
        ];

        return array_merge($dataDefault, $data);
    }

    // Render HTML final (template + variable) → buat preview / PDF
    public function renderHtml(): string
    {
        $html = $this->type->template_html ?? '';
        $vars = $this->buildTemplateVariables();

        foreach ($vars as $key => $value) {
            $html = str_replace('{{'.$key.'}}', e($value), $html);
        }

        return $html;
    }
}
