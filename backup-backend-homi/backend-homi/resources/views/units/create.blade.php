@extends('layouts.app')

@section('title','Tambah Unit')
@section('page_title','Tambah Unit')
@section('page_subtitle','Tambahkan data unit baru')

@section('content')
<div class="mx-auto max-w-2xl space-y-5">
    <div>
        <div class="homi-title">Tambah Unit</div>
        <div class="homi-subtitle">Isi informasi unit secara ringkas dan jelas.</div>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="homi-card">
        <form action="{{ route('units.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="mb-1 block text-sm font-semibold text-slate-700">Kode Unit <span class="text-rose-600">*</span></label>
                <input id="code"
                       type="text"
                       name="code"
                       value="{{ old('code') }}"
                       placeholder="Contoh: D1-07"
                       required
                       class="w-full rounded-lg border px-3 py-2.5 text-sm">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="block" class="mb-1 block text-sm font-semibold text-slate-700">Blok</label>
                    <input id="block"
                           type="text"
                           name="block"
                           value="{{ old('block') }}"
                           placeholder="Opsional"
                           class="w-full rounded-lg border px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label for="floor" class="mb-1 block text-sm font-semibold text-slate-700">Lantai</label>
                    <input id="floor"
                           type="number"
                           name="floor"
                           value="{{ old('floor') }}"
                           placeholder="Opsional"
                           class="w-full rounded-lg border px-3 py-2.5 text-sm">
                </div>
            </div>

            <div class="flex flex-col gap-2 pt-2 sm:flex-row sm:justify-end">
                <a href="{{ route('units.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-[var(--homi-orange)] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e67949]">
                    Simpan Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
