@extends('layouts.app')

@section('title','Tambah Staff')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="homi-card p-0 overflow-hidden shadow-2xl shadow-slate-200 border-none">
        {{-- Header --}}
        <div class="bg-slate-900 p-8 text-white relative">
            <div class="absolute right-0 top-0 p-8 opacity-10">
                <svg viewBox="0 0 24 24" class="h-24 w-24 fill-current"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-2xl font-black tracking-tight mb-2 uppercase">Registrasi Staff</h1>
                <p class="text-slate-400 text-sm font-medium">Tambahkan administrator atau pengelola baru</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.staff.store') }}" class="p-8 space-y-6">
            @csrf

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold uppercase tracking-widest">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Nama Panggilan/Singkat <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Admin" 
                           class="homi-input font-bold" required>
                </div>

                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Masukkan nama lengkap..." 
                           class="homi-input font-bold">
                </div>

                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="staff@example.com" 
                           class="homi-input font-bold" required>
                </div>

                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Username <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="username" 
                           class="homi-input font-bold" required>
                </div>

                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Role <span class="text-rose-500">*</span></label>
                    <select name="role" class="homi-input font-bold" required>
                        <option value="admin" @selected(old('role') == 'admin')>Admin Toko/Perumahan</option>
                        <option value="superadmin" @selected(old('role') == 'superadmin')>Owner / Super Admin</option>
                    </select>
                </div>

                {{-- Ditambahkan: Pilih Perumahan (Hanya muncul jika bukan superadmin) --}}
                <div class="space-y-2 col-span-1 md:col-span-2" id="tenant-select-container">
                    <label class="homi-label text-slate-900">Ditugaskan di Perumahan <span class="text-rose-500">*</span></label>
                    <select name="tenant_id" class="homi-input font-bold">
                        <option value="">-- Pilih Perumahan --</option>
                        @foreach($tenants as $ten)
                            <option value="{{ $ten->id }}" @selected(old('tenant_id') == $ten->id)>{{ $ten->name }} ({{ $ten->code }})</option>
                        @endforeach
                    </select>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic pt-1">Kosongkan jika bukan admin perumahan (super admin)</p>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-50">
                    <div class="space-y-2">
                        <label class="homi-label text-slate-900">Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" placeholder="••••••••" 
                               class="homi-input font-bold" required>
                    </div>

                    <div class="space-y-2">
                        <label class="homi-label text-slate-900">Konfirmasi Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="••••••••" 
                               class="homi-input font-bold" required>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end items-center gap-4 pt-8 border-t border-slate-100">
                <a href="{{ route('admin.staff.index') }}" class="w-full sm:w-auto text-center px-10 py-3 rounded-2xl text-sm font-black text-slate-400 uppercase tracking-[0.2em] hover:text-slate-600 transition-colors">
                    Batalkan
                </a>
                <button type="submit" class="w-full sm:w-auto px-12 py-4 rounded-[1.5rem] bg-slate-900 text-white text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:shadow-[var(--homi-blue-light)] hover:bg-[var(--homi-blue)] transition-all">
                    Simpan Data Staff
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.querySelector('select[name="role"]');
        const tenantContainer = document.getElementById('tenant-select-container');
        const tenantSelect = tenantContainer.querySelector('select');

        function toggleTenant() {
            if (roleSelect.value === 'superadmin') {
                tenantContainer.style.display = 'none';
                tenantSelect.removeAttribute('required');
            } else {
                tenantContainer.style.display = 'block';
                tenantSelect.setAttribute('required', 'required');
            }
        }

        roleSelect.addEventListener('change', toggleTenant);
        toggleTenant(); // run on load
    });
</script>
@endpush
