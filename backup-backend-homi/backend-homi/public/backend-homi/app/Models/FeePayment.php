<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'invoice_id','payer_user_id','proof_path','note',
        'review_status','reviewed_by','reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function invoice() { return $this->belongsTo(FeeInvoice::class, 'invoice_id'); }
    public function payer() { return $this->belongsTo(User::class, 'payer_user_id'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
