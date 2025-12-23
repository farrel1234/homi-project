<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // fleksibel: role_id==1 ATAU role=='admin'
        $isAdmin = false;

        if (isset($user->role_id) && (int) $user->role_id === 1) $isAdmin = true;
        if (isset($user->role) && in_array(strtolower((string) $user->role), ['admin', 'superadmin'], true)) $isAdmin = true;

        if (!$isAdmin) {
            return response()->json(['message' => 'Forbidden (admin only)'], 403);
        }

        return $next($request);
    }
}
