<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $table = 'fee_payments';

    // biar create()/update() aman tanpa fillable ribet
    protected $guarded = [];

    // optional biar reviewed_at kebaca sebagai datetime
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'invoice_id');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_user_id');
    }

    // ✅ INI YANG HILANG → reviewer = admin yang verifikasi
    public function reviewer()
    {
        // kolom yang kamu update di ReviewController adalah reviewed_by
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
