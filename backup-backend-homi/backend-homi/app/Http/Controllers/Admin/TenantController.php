<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Support\Tenancy\TenantManager;

class TenantController extends Controller
{
    public function index()
    {
        $items = Tenant::orderBy('name')->get();
        return view('tenants.index', compact('items'));
    }

    public function create()
    {
        $prefill = session('prefill_tenant');
        return view('tenants.create', compact('prefill'));
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
        $data['db_host'] = $data['db_host'] ?? config('database.connections.central.host', '127.0.0.1');
        $data['plan']    = 'trial';
        $data['trial_ends_at'] = now()->addDays(config('plans.default_trial_days', 30));

        $tenant = Tenant::create($data);

        // Jika berasal dari permintaan trial, update status permintaan & bersihkan session
        if ($request->has('request_id')) {
            \App\Models\TenantRequest::where('id', $request->request_id)->update(['status' => 'approved']);
            session()->forget('prefill_tenant');
        }

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
            'plan'        => 'required|string|in:trial,starter,professional,elite',
        ]);

        $tenant->update($request->all());

        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil diperbarui.');
    }



    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil dihapus.');
    }

    /**
     * Jalankan migrasi tabel ke database tenant yang baru.
     */
    public function migrateDatabase(Tenant $tenant)
    {
        try {
            // Ciptakan database fisik jika belum ada di server (Laragon) menggunakan koneksi Central
            $dbName = $tenant->db_database;
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Aktifkan koneksi database (Menggunakan fitur Global Hijack di TenantManager)
            $manager = app(\App\Support\Tenancy\TenantManager::class);
            $manager->activate($tenant);

            // Jalankan migrasi menggunakan koneksi default yang sudah diarahkan ke database tenant
            Artisan::call('migrate', [
                '--force'    => true,
            ]);

            // Jalankan seeder untuk mengisi akun Admin default dan data awal
            Artisan::call('db:seed', [
                '--force'    => true,
            ]);

            return redirect()->route('tenants.index')->with('success', "Database perumahan {$tenant->name} berhasil di-setup!");
        } catch (\Exception $e) {
            return redirect()->route('tenants.index')->with('error', "Gagal Setup Database: " . $e->getMessage());
        }
    }
}
