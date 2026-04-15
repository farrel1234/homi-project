<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Require Tenant Context
    |--------------------------------------------------------------------------
    |
    | Jika true, semua route API (kecuali yang dikecualikan) wajib membawa
    | tenant code via header/body/query atau domain mapping.
    |
    */
    'required' => env('TENANCY_REQUIRED', true),

    /*
    |--------------------------------------------------------------------------
    | Fallback Tenant Code
    |--------------------------------------------------------------------------
    |
    | Dipakai untuk mode transisi/single-tenant saat client belum mengirimkan
    | tenant code. Kosongkan di production multi-tenant.
    |
    */
    'fallback_tenant_code' => env('TENANCY_FALLBACK_TENANT_CODE'),

    /*
    |--------------------------------------------------------------------------
    | Tenant Resolver Inputs
    |--------------------------------------------------------------------------
    */
    'header_keys' => [
        'X-Tenant-Code',
        'X-Housing-Code',
    ],

    'payload_keys' => [
        'tenant_code',
        'tenant',
        'housing_code',
    ],

    /*
    |--------------------------------------------------------------------------
    | Optional Hostname Mapping
    |--------------------------------------------------------------------------
    |
    | Jika true, resolver akan mencoba mencocokkan request host ke kolom
    | `domain` di tabel tenants saat tenant_code tidak ditemukan.
    |
    */
    'lookup_by_domain' => env('TENANCY_LOOKUP_BY_DOMAIN', true),

    /*
    |--------------------------------------------------------------------------
    | API Paths Excluded From Tenant Resolution
    |--------------------------------------------------------------------------
    */
    'exempt_paths' => [
        'api/ping',
        'api/__debug/php',
        'api/tenant-requests',
        'api/tenants',
    ],
];
