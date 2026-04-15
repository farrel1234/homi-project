<?php

/**
 * Konfigurasi Paket & Fitur Homi Project (Synced with Landing Page).
 * 
 * Setiap perumahan (Tenant) memiliki plan (trial, starter, professional, elite).
 * Fitur-fitur di bawah ini akan di-cek lewat middleware CheckTenantFeature.
 */

return [
    'default_trial_days' => 30,

    'plans' => [
        'trial' => [
            'name' => 'Trial - 30 Hari Akses Penuh',
            'features' => [
                'letter-request',
                'service-request',
                'complaint-basic',
                'fee-payment',
                'pdf-download',
                'ai-analysis',
                'priority-support',
            ],
        ],

        'starter' => [
            'name' => 'Starter - Up to 100 Houses',
            'features' => [
                'letter-request',
                'service-request',
                'complaint-basic',
                'fee-payment',
            ],
        ],

        'professional' => [
            'name' => 'Professional - Up to 300 Houses',
            'features' => [
                'letter-request',
                'service-request',
                'complaint-basic',
                'fee-payment',
                'pdf-download',
                'priority-support',
                'mobile-app',
            ],
        ],

        'elite' => [
            'name' => 'Elite / Enterprise - Unlimited',
            'features' => [
                'letter-request',
                'service-request',
                'complaint-basic',
                'fee-payment',
                'pdf-download',
                'ai-analysis',
                'priority-support',
                'mobile-app',
                'white-label',
                'dedicated-instance',
            ],
        ],
    ],
];
