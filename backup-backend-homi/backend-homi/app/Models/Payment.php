<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'fee_payments';

    protected $fillable = [
        'invoice_id',
        'payer_user_id',
        'proof_path',
        'note',
        'review_status', // pending/approved/rejected
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // Relasi asli (kamu sudah punya)
    public function user()
    {
        return $this->belongsTo(User::class, 'payer_user_id');
    }

    // ✅ Alias biar blade versi baru (yang pakai payer) juga bisa jalan
    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'invoice_id');
    }

    // ==========
    // Bridge fields untuk blade lama (boleh tetap)
    // ==========

    // amount ada di invoice
    public function getAmountAttribute()
    {
        return $this->invoice?->amount;
    }

    public function getDueDateAttribute()
    {
        return $this->invoice?->due_date;
    }

    // mapping status untuk UI lama
    public function getStatusAttribute()
    {
        return match ($this->review_status) {
            'approved' => 'paid',
            'rejected' => 'failed',
            default    => 'pending',
        };
    }

    // ✅ tanggal dibayar = waktu warga upload bukti (created_at)
    public function getPaidAtAttribute()
    {
        return $this->created_at;
    }

    public function getAdminNoteAttribute()
    {
        return $this->note ?? '';
    }

    public function getDescriptionAttribute()
    {
        return $this->note ?? '';
    }

    public function getPaymentMethodAttribute()
    {
        return 'QRIS';
    }
}
