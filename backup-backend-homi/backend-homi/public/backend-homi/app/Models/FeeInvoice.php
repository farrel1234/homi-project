<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeInvoice extends Model
{
    protected $fillable = [
        'user_id',
        'fee_type_id',
        'period',
        'amount',
        'status',
        'trx_id',
        'issued_by',
        'due_date',
    ];

    protected $casts = [
        'period'   => 'date',
        'due_date' => 'date',
    ];

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }
}
