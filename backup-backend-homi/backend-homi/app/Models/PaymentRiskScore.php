<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRiskScore extends Model
{
    protected $table = 'payment_risk_scores';

    protected $fillable = [
        'user_id',
        'period',
        'risk',
        'predicted_delinquent',
        'features_json',
        'computed_at',
    ];

    protected $casts = [
        'period' => 'date',
        'risk' => 'decimal:4',
        'predicted_delinquent' => 'boolean',
        'features_json' => 'array',
        'computed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
