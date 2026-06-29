<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use App\Support\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function __construct(
        protected TenantManager $tenantManager,
        protected TenantService $tenantService
    ) {}

    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $items = User::query()
            ->where(function($query) {
                $query->where('role', 'admin')
                      ->orWhere('role', 'superadmin')
                      ->orWhere('role_id', 1);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('full_name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%")
                       ->orWhere('username', 'like', "%{$q}%");
                });
            })
            ->with('tenant')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.staff.index', compact('items', 'q'));
    }

    public function create()
    {
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get();
        return view('admin.staff.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username'  => ['required', 'string', 'max:255', 'unique:users,username'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', 'in:admin,superadmin'],
            'tenant_id' => ['nullable', 'required_if:role,admin', 'exists:central.tenants,id'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'username'  => $data['username'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'role_id'   => $data['role'] === 'admin' ? 1 : 0, 
            'tenant_id' => $data['role'] === 'admin' ? $data['tenant_id'] : null,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // SYNC: Jika role admin, duplikat ke database tenant
        if ($user->role === 'admin' && $user->tenant_id) {
            $tenant = $this->tenantService->findById($user->tenant_id);
            if ($tenant) {
                $this->tenantManager->activate($tenant);
                
                // Buat di DB Tenant (Hanya jika belum ada dengan email yang sama)
                User::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'name'      => $user->name,
                        'full_name' => $user->full_name,
                        'username'  => $user->username,
                        'password'  => $user->password, // Sudah ter-hash dari central
                        'role'      => 'admin',
                        'role_id'   => 1,
                        'is_active' => 1,
                        'is_verified' => true,
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]
                );

                $this->tenantManager->deactivate();
            }
        }

        return redirect()->route('admin.staff.index')->with('ok', 'Staff berhasil ditambahkan.');
    }

    public function edit(User $staff)
    {
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get();
        return view('admin.staff.edit', ['item' => $staff, 'tenants' => $tenants]);
    }

    public function update(Request $request, User $staff)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'username'  => ['required', 'string', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', 'in:admin,superadmin'],
            'tenant_id' => ['nullable', 'required_if:role,admin', 'exists:central.tenants,id'],
        ]);

        $updateData = [
            'name'      => $data['name'],
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'username'  => $data['username'],
            'role'      => $data['role'],
            'role_id'   => $data['role'] === 'admin' ? 1 : 0,
            'tenant_id' => $data['role'] === 'admin' ? $data['tenant_id'] : null,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $oldTenantId = $staff->tenant_id;
        $staff->update($updateData);

        // SYNC: Update di database tenant
        if ($staff->role === 'admin' && $staff->tenant_id) {
            $tenant = $this->tenantService->findById($staff->tenant_id);
            if ($tenant) {
                // Jika ganti perumahan, hapus dari yang lama
                if ($oldTenantId && $oldTenantId != $staff->tenant_id) {
                    $oldTenant = $this->tenantService->findById($oldTenantId);
                    if ($oldTenant) {
                        $this->tenantManager->activate($oldTenant);
                        User::where('email', $staff->email)->delete();
                        $this->tenantManager->deactivate();
                    }
                }

                // Update/Create di perumahan baru
                $this->tenantManager->activate($tenant);
                User::updateOrCreate(
                    ['email' => $staff->email],
                    [
                        'name'      => $staff->name,
                        'full_name' => $staff->full_name,
                        'username'  => $staff->username,
                        'password'  => $staff->password,
                        'role'      => 'admin',
                        'role_id'   => 1,
                        'is_active' => 1,
                        'is_verified' => true,
                        'email_verified_at' => $staff->email_verified_at ?? now(),
                    ]
                );
                $this->tenantManager->deactivate();
            }
        }

        return redirect()->route('admin.staff.index')->with('ok', 'Data staff berhasil diperbarui.');
    }

    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        if ($staff->role === 'admin' && $staff->tenant_id) {
            $tenant = $this->tenantService->findById($staff->tenant_id);
            if ($tenant) {
                $this->tenantManager->activate($tenant);
                User::where('email', $staff->email)->delete();
                $this->tenantManager->deactivate();
            }
        }

        $staff->delete();
        return redirect()->route('admin.staff.index')->with('ok', 'Staff berhasil dihapus.');
    }
}
