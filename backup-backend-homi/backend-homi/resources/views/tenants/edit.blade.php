@extends('layouts.app')

@section('title', 'Edit Tenant')
@section('page_title', 'Edit Tenant')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tenants.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[var(--homi-blue)] transition-colors">
            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Update Informasi Tenant</h3>
            <p class="text-sm text-slate-500 mt-1">Mengubah koneksi database dapat menyebabkan aplikasi tidak bisa diakses jika data salah.</p>
        </div>
        
        <form action="{{ route('tenants.update', $tenant->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Nama Tenant <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Kode Tenant (Unique) <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $tenant->code) }}" required
                           class="w-full rounded-xl border-slate-200 text-sm font-mono focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                    @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700 text-orange-600">Kode Registrasi (Secret Code untuk Warga) <span class="text-red-500">*</span></label>
                <input type="text" name="registration_code" value="{{ old('registration_code', $tenant->registration_code) }}" required
                       class="w-full rounded-xl border-orange-200 bg-orange-50/30 text-sm font-bold focus:border-orange-400 focus:ring-orange-400"
                       placeholder="HW-2024-SEC">
                <p class="text-[10px] text-slate-500 italic">Kode ini diberikan kepada warga perumahan agar bisa mendaftar di aplikasi mobile.</p>
                @error('registration_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Domain (Optional)</label>
                <input type="text" name="domain" value="{{ old('domain', $tenant->domain) }}"
                       class="w-full rounded-xl border-slate-200 text-sm focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                @error('domain') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="border-slate-100">

            <div class="p-4 rounded-xl bg-amber-50 border border-amber-100 mb-2">
                <p class="text-[11px] text-amber-800 font-bold uppercase tracking-wider">Peringatan!</p>
                <p class="text-xs text-amber-700 mt-0.5">Password database tidak akan diubah jika dikosongkan.</p>
            </div>

            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Nama Database <span class="text-red-500">*</span></label>
                <input type="text" name="db_database" value="{{ old('db_database', $tenant->db_database) }}" required
                       class="w-full rounded-xl border-slate-200 text-sm font-mono focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                @error('db_database') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Username DB <span class="text-red-500">*</span></label>
                    <input type="text" name="db_username" value="{{ old('db_username', $tenant->db_username) }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                    @error('db_username') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Password DB (Kosongkan jika tetap)</label>
                    <input type="password" name="db_password"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]"
                           placeholder="••••••••">
                    @error('db_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="rounded-xl bg-[var(--homi-blue)] px-8 py-3 text-sm font-bold text-white shadow-lg shadow-sky-900/10 hover:bg-sky-700 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
