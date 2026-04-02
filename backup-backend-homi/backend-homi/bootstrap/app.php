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
        
        // Menambahkan security headers secara global
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
        ]);

        // Exclude logout dari CSRF untuk menghindari error 419 saat session expired
        $middleware->validateCsrfTokens(except: [
            'admin/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
