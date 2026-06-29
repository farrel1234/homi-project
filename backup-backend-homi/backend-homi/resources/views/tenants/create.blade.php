@extends('layouts.app')

@section('title', 'Tambah Tenant')
@section('page_title', 'Tambah Tenant Baru')

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
            <h3 class="text-lg font-bold text-slate-800">Informasi Tenant & Database</h3>
            <p class="text-sm text-slate-500 mt-1">Pastikan database sudah dibuat di server sebelum menambahkan tenant.</p>
            @if(isset($prefill))
                <div class="mt-4 p-4 rounded-xl bg-blue-50 border border-blue-100 flex items-center gap-4">
                    <div class="h-10 w-10 bg-white rounded-lg flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-2"><path d="m12 3 1.912 5.886h6.19l-5.008 3.638 1.912 5.886L12 14.772l-5.006 3.638 1.912-5.886-5.008-3.638h6.19z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-blue-400 leading-none mb-1">Magic Link Active</p>
                        <p class="text-xs font-bold text-blue-900">Data otomatis terisi dari permintaan trial <b>{{ $prefill['name'] }}</b>.</p>
                    </div>
                </div>
            @endif
        </div>
        
        <form action="{{ route('tenants.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            @if(isset($prefill['request_id']))
                <input type="hidden" name="request_id" value="{{ $prefill['request_id'] }}">
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Nama Tenant <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $prefill['name'] ?? '') }}" required
                           class="w-full rounded-xl border border-slate-200 text-sm focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] shadow-sm"
                           placeholder="Contoh: Hawaii Garden">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Kode Tenant (Slug/ID) <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', isset($prefill) ? \Illuminate\Support\Str::slug($prefill['name']) : '') }}" required
                           class="w-full rounded-xl border border-slate-200 text-sm font-mono focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] shadow-sm"
                           placeholder="hawaii-garden">
                    @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700 text-orange-600">Kode Registrasi (Secret Code untuk Warga) <span class="text-red-500">*</span></label>
                <input type="text" name="registration_code" value="{{ old('registration_code', isset($prefill) ? strtoupper(substr($prefill['name'], 0, 3)) . '-' . rand(1000, 9999) : '') }}" required
                       class="w-full rounded-xl border border-orange-200 bg-orange-50/30 text-sm font-bold focus:border-orange-400 focus:ring-orange-400 shadow-sm"
                       placeholder="Contoh: HW-2024-SEC">
                <p class="text-[10px] text-slate-500 italic">Kode ini diberikan kepada warga perumahan agar bisa mendaftar di aplikasi mobile.</p>
                @error('registration_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Domain (Optional)</label>
                <input type="text" name="domain" value="{{ old('domain') }}"
                       class="w-full rounded-xl border border-slate-200 text-sm focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] shadow-sm"
                       placeholder="hawaii.homiapp.id">
                @error('domain') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="border-slate-100">

            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Nama Database <span class="text-red-500">*</span></label>
                <input type="text" name="db_database" value="{{ old('db_database') }}" required
                       class="w-full rounded-xl border border-slate-200 text-sm font-mono focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] shadow-sm"
                       placeholder="homi_hawaii_db">
                @error('db_database') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Username DB <span class="text-red-500">*</span></label>
                    <input type="text" name="db_username" value="{{ old('db_username') }}" required
                           class="w-full rounded-xl border border-slate-200 text-sm focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] shadow-sm"
                           placeholder="root / u_hawaii">
                    @error('db_username') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Password DB</label>
                    <input type="password" name="db_password"
                           class="w-full rounded-xl border border-slate-200 text-sm focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] shadow-sm"
                           placeholder="••••••••">
                    @error('db_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="rounded-xl bg-[var(--homi-blue)] px-8 py-3 text-sm font-bold text-white shadow-lg shadow-sky-900/10 hover:bg-sky-700 transition-all">
                    Simpan Tenant
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.querySelector('input[name="name"]');
        const codeInput = document.querySelector('input[name="code"]');
        const dbInput = document.querySelector('input[name="db_database"]');
        const dbUser = document.querySelector('input[name="db_username"]');
        const regInput = document.querySelector('input[name="registration_code"]');

        const isPrefill = @json(isset($prefill));

        // Hanya jalankan auto-suggest jika ini bukan mode PREFILL (Magic Link)
        // Jika prefill, biarkan data dari session yang menang.
        if (!isPrefill) {
            nameInput.addEventListener('input', function() {
                const name = this.value;
                const slug = name.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                
                // Update Code/Slug
                codeInput.value = slug;

                // Update Database Name (homi_xxx_db)
                if (slug) {
                    dbInput.value = 'homi_' + slug.replace(/-/g, '_') + '_db';
                    dbUser.value = 'root'; // Default Laragon
                } else {
                    dbInput.value = '';
                    dbUser.value = '';
                }

                // Update Reg Code (Contoh: GRY-1234)
                if (name.length >= 3) {
                    const prefix = name.substring(0, 3).toUpperCase();
                    if (!regInput.value || regInput.value.includes('-')) {
                         // Hanya update jika belum diotak-atik manual secara mendalam
                         // (Sederhana saja untuk UX)
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
