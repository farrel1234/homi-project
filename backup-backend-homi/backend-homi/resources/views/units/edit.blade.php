@extends('layouts.app')

@section('title','Edit Unit')
@section('page_title','Edit Unit')
@section('page_subtitle','Perbarui data unit yang dipilih')

@section('content')
<div class="mx-auto max-w-2xl space-y-5">
    <div>
        <div class="homi-title">Edit Unit {{ $unit->code }}</div>
        <div class="homi-subtitle">Pastikan kode unit tetap unik dan mudah dikenali.</div>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="homi-card">
        <form action="{{ route('units.update', $unit->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="code" class="mb-1 block text-sm font-semibold text-slate-700">Kode Unit <span class="text-rose-600">*</span></label>
                <input id="code"
                       type="text"
                       name="code"
                       value="{{ old('code', $unit->code) }}"
                       required
                       class="w-full rounded-lg border px-3 py-2.5 text-sm">
                <p class="mt-1 text-xs text-slate-500">Kode unit harus unik.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="block" class="mb-1 block text-sm font-semibold text-slate-700">Blok</label>
                    <input id="block"
                           type="text"
                           name="block"
                           value="{{ old('block', $unit->block) }}"
                           class="w-full rounded-lg border px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label for="floor" class="mb-1 block text-sm font-semibold text-slate-700">Lantai</label>
                    <input id="floor"
                           type="number"
                           name="floor"
                           value="{{ old('floor', $unit->floor) }}"
                           class="w-full rounded-lg border px-3 py-2.5 text-sm">
                </div>
            </div>

            <div class="flex flex-col gap-2 pt-2 sm:flex-row sm:justify-end">
                <a href="{{ route('units.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Kembali
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-[var(--homi-orange)] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e67949]">
                    Update Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
