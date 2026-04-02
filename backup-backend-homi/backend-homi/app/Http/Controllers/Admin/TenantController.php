<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $items = Tenant::orderBy('name')->get();
        return view('tenants.index', compact('items'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:central.tenants,code',
            'registration_code' => 'nullable|string|max:50',
            'domain' => 'nullable|string|max:255',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['db_host'] = $data['db_host'] ?? '127.0.0.1';

        Tenant::create($data);

        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil ditambahkan.');
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:central.tenants,code,' . $tenant->id,
            'registration_code' => 'nullable|string|max:50',
            'domain' => 'nullable|string|max:255',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
        ]);

        $tenant->update($request->all());

        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil diperbarui.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil dihapus.');
    }
}
