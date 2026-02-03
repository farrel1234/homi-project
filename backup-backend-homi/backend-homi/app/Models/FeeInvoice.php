<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInvoice extends Model
{
    protected $table = 'fee_invoices';

    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
        'total_amount' => 'integer',
        'nominal' => 'integer',
        'period' => 'date',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }
}
