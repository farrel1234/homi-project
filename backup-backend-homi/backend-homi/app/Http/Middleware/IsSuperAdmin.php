<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('admin.login');
        }

        if (!$user->isSuperAdmin()) {
            return abort(403, 'Akses khusus Admin Pusat.');
        }

        return $next($request);
    }
}
