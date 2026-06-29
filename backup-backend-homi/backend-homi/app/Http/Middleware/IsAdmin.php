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
        if (isset($user->role) && strtolower((string) $user->role) === 'admin') $isAdmin = true;

        if (!$isAdmin) {
            // Jika dia Super Admin tapi nyasar ke route Admin Perumahan
            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Admin Pusat tidak memiliki akses ke fitur perumahan spesifik.');
            }
            return abort(403, 'Akses khusus Admin Perumahan.');
        }

        return $next($request);
    }
}
