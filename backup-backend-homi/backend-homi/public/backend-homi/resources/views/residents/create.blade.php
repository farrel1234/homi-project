@extends('layouts.app')

@section('title','Tambah Warga')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Tambah Data Warga</h1>
        <p class="homi-subtitle">Tambahkan warga ke direktori. Username dibuat otomatis dari email.</p>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="homi-card bg-rose-50 border border-rose-200 text-sm text-rose-800">
            <div class="font-semibold mb-1">Gagal menyimpan</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="homi-card border border-gray-200">
        <form method="POST" action="{{ route('residents.store') }}" class="space-y-6">
            @csrf

            {{-- DATA UTAMA (MINIMAL) --}}
            <div class="grid md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Full Name <span class="text-rose-600">*</span></label>
                    <input type="text" name="full_name"
                           value="{{ old('full_name') }}"
                           placeholder="Contoh: Hanif Abyad"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]"
                           required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Email <span class="text-rose-600">*</span></label>
                    <input type="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="Contoh: hanif@gmail.com"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]"
                           required>
                    <div class="text-[11px] text-gray-500 mt-1">
                        Username akan dibuat otomatis dari email (bisa diedit setelah dibuat).
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">No HP</label>
                    <input type="text" name="phone"
                           value="{{ old('phone') }}"
                           placeholder="Contoh: 0812xxxxxxx"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Blok</label>
                    <input type="text" name="blok"
                           value="{{ old('blok') }}"
                           placeholder="Contoh: A"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">No Rumah</label>
                    <input type="text" name="no_rumah"
                           value="{{ old('no_rumah') }}"
                           placeholder="Contoh: 07"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-start gap-3 rounded-xl border border-gray-200 p-3 hover:bg-gray-50">
                        <input type="checkbox" name="is_public" value="1" class="mt-1"
                               @checked(old('is_public'))>
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                Tampilkan di Direktori Warga (Publik)
                            </div>
                            <div class="text-[12px] text-gray-500">
                                Jika aktif, warga lain dapat melihat data rumah ini di menu direktori.
                            </div>
                        </div>
                    </label>
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="flex flex-wrap gap-2 pt-2">
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white text-sm font-semibold hover:bg-sky-800">
                    Simpan
                </button>

                <a href="{{ route('residents.index') }}"
                   class="px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-600 hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
