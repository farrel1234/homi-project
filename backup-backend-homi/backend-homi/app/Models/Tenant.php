<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'registration_code',
        'domain',
        'db_driver',
        'db_host',
        'db_port',
        'db_database',
        'db_username',
        'db_password',
        'is_active',
        'plan',
        'trial_ends_at',
    ];

    protected $casts = [
        'db_port' => 'integer',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Cek apakah masih dalam masa trial.
     */
    public function isTrialActive(): bool
    {
        return $this->plan === 'trial' 
            && $this->trial_ends_at 
            && now()->lessThan($this->trial_ends_at);
    }

    /**
     * Cek apakah tenant memiliki akses ke fitur tertentu.
     */
    public function hasFeature(string $feature): bool
    {
        // 1. Jika Trial Aktif, izinkan saja (Fitur All-Access)
        if ($this->isTrialActive()) {
            return true;
        }

        // 2. Jika Trial Telah Habis, turunkan ke STARTER (Meskipun status di DB masih trial)
        $currentPlan = $this->plan;
        if ($currentPlan === 'trial' && !$this->isTrialActive()) {
            $currentPlan = 'starter';
        }

        // 3. Ambil mapping fitur dari config/plans.php
        $planConfig = config("plans.plans.$currentPlan.features");

        return in_array($feature, (array) $planConfig);
    }
}
