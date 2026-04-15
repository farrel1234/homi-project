<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantRequest;
use Illuminate\Http\Request;

class TenantRequestController extends Controller
{
    /**
     * Tampilkan daftar permintaan trial.
     */
    public function index()
    {
        $requests = TenantRequest::orderBy('created_at', 'desc')->get();
        return view('admin.tenant_requests.index', compact('requests'));
    }

    /**
     * Ubah status permintaan (misal: Approve / Reject).
     */
    public function updateStatus(Request $request, TenantRequest $tenantRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $tenantRequest->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $tenantRequest->notes,
        ]);

        return redirect()->back()->with('success', 'Status permintaan berhasil diperbarui.');
    }

    /**
     * Arahkan ke form pendaftaran tenant dengan data yang sudah di-prefill.
     */
    public function approve(TenantRequest $tenantRequest)
    {
        // Menyimpan data request ke session untuk pre-fill form di TenantController@create
        session(['prefill_tenant' => [
            'name' => $tenantRequest->name,
            'email' => $tenantRequest->email,
            'manager_name' => $tenantRequest->manager_name,
            'phone' => $tenantRequest->phone,
            'request_id' => $tenantRequest->id,
        ]]);

        return redirect()->route('tenants.create');
    }

    /**
     * Hapus permintaan trial.
     */
    public function destroy(TenantRequest $tenantRequest)
    {
        $tenantRequest->delete();
        return redirect()->back()->with('success', 'Permintaan trial telah dihapus.');
    }
}
