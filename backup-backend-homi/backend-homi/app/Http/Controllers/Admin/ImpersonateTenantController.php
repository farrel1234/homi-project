<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ImpersonateTenantController extends Controller
{
    /**
     * Switch context to a specific tenant (Super Admin only).
     */
    public function switch(Request $request, $id)
    {
        // Pastikan hanya Super Admin yang bisa (Auth middleware di route)
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($id == 'central') {
            session()->forget('impersonated_tenant_id');
            return redirect()->route('admin.dashboard')->with('success', 'Kembali ke Dashboard Pusat.');
        }

        $tenant = Tenant::findOrFail($id);
        session()->put('impersonated_tenant_id', $tenant->id);

        return redirect()->route('admin.dashboard')->with('success', "Berhasil masuk ke Dashboard {$tenant->name}.");
    }
}
