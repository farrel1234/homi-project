@extends('layouts.app')

@section('title','Tambah Warga')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
<div class="max-w-3xl mx-auto py-8">
    <div class="homi-card p-0 overflow-hidden shadow-2xl shadow-slate-200 border-none">
        {{-- Header dengan Visual Premium --}}
        <div class="bg-slate-900 p-8 text-white relative">
            <div class="absolute right-0 top-0 p-8 opacity-10">
                <svg viewBox="0 0 24 24" class="h-24 w-24 fill-current"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-2xl font-black tracking-tight mb-2 uppercase">Registrasi Warga</h1>
                <p class="text-slate-400 text-sm font-medium">Username akan digenerate otomatis melalui alamat email</p>
            </div>
        </div>

        <form method="POST" action="{{ route('residents.store') }}" class="p-8 space-y-8">
            @csrf

            {{-- Row 1: Identitas Dasar --}}
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <label class="homi-label text-slate-900">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Masukkan nama lengkap sesuai KTP..." 
                               class="homi-input text-lg font-bold" required>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="homi-label text-slate-900">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="warga@example.com" 
                               class="homi-input font-bold" required>
                    </div>

                    <div class="space-y-2">
                        <label class="homi-label">Nomor Telepon/WA</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0812XXXXXXXX" 
                               class="homi-input font-mono font-bold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="homi-label">Blok</label>
                            <input type="text" name="blok" value="{{ old('blok') }}" placeholder="A" 
                                   class="homi-input font-bold text-center uppercase">
                        </div>
                        <div class="space-y-2">
                            <label class="homi-label">No. Rumah</label>
                            <input type="text" name="no_rumah" value="{{ old('no_rumah') }}" placeholder="01" 
                                   class="homi-input font-bold text-center">
                        </div>
                    </div>
                </div>

                {{-- Row 2: Keamanan & Privasi --}}
                <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100 shadow-inner">
                    <label class="flex items-start gap-4 cursor-pointer group">
                        <input type="checkbox" name="is_public" value="1" @checked(old('is_public', true)) 
                               class="mt-1 h-6 w-6 rounded-lg border-slate-300 text-[var(--homi-blue)] focus:ring-[var(--homi-blue)] transition-all">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-700 uppercase tracking-widest leading-none mb-1">Tampilkan di Direktori</span>
                            <span class="text-[11px] text-slate-400 font-bold uppercase">Warga lain dapat melihat data rumah ini di aplikasi</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Row 3: Actions --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end items-center gap-4 pt-8 border-t border-slate-100">
                <a href="{{ route('residents.index') }}" class="w-full sm:w-auto text-center px-10 py-3 rounded-2xl text-sm font-black text-slate-400 uppercase tracking-[0.2em] hover:text-slate-600 transition-colors">
                    Batalkan
                </a>
                <button type="submit" class="w-full sm:w-auto px-12 py-4 rounded-[1.5rem] bg-slate-900 text-white text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:shadow-[var(--homi-blue-light)] hover:bg-[var(--homi-blue)] transition-all">
                    Simpan Data Warga
                </button>
            </div>
        </form>
    </div>
</div>


</div>
@endsection
