<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // CSRF Exemptions (SATU KALI SAJA, jangan duplikat)
        $middleware->validateCsrfTokens(except: [
            'admin/login*',
            'admin/logout',
            'api/tenant-requests',
        ]);

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Group middleware untuk Web (Admin)
        $middleware->web(append: [
            \App\Http\Middleware\TenantMiddleware::class,
        ]);

        // Group middleware untuk API
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\ResolveTenant::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
        ]);

        // Alias middleware custom
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'tenant' => \App\Http\Middleware\ResolveTenant::class,
            'check_feature' => \App\Http\Middleware\CheckTenantFeature::class,
        ]);

        // KRITIS: Pastikan TenantMiddleware berjalan SEBELUM Authenticate 
        // Jika tidak, Authenticate akan memeriksa auth di database pusati SEBELUM
        // TenantMiddleware sempat menswitch database tenant, lalu me-redirect-nya lagi.
        $middleware->prependToPriorityList(
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \App\Http\Middleware\TenantMiddleware::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
