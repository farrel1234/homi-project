<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'description',
        'due_date',
        'paid_at',
        'status',
        'payment_method',
        'payment_reference',
        'admin_note', // kalau kolom ini belum ada di DB, boleh dihapus dari sini
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELASI ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Detail item pembayaran (payment_items)
    public function items()
    {
        return $this->hasMany(PaymentItem::class);
    }

    // (opsional) relasi ke transaksi gateway
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
