@extends('layouts.app')

@section('title','Detail Warga')

@section('content')
@php($u = $item->user)
<div class="space-y-6">

    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Detail Warga</h1>
        <p class="homi-subtitle">Lihat data akun warga & data rumah.</p>
    </div>

    @if(session('ok'))
        <div class="homi-card bg-emerald-50 border-emerald-200 text-sm text-emerald-800">
            {{ session('ok') }}
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-4">

        <div class="md:col-span-2 space-y-4">
            <div class="homi-card space-y-3">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">Data Akun</div>

                <div class="grid md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Nama</div>
                        <div class="text-gray-900 font-semibold">{{ $u->full_name ?? $u->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Username</div>
                        <div class="text-gray-800">{{ $u->username ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Email</div>
                        <div class="text-gray-800">{{ $u->email ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">No HP</div>
                        <div class="text-gray-800">{{ $u->phone ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="homi-card space-y-3">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">Data Rumah</div>

                <div class="grid md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Blok</div>
                        <div class="text-gray-800">{{ $item->blok ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Nomor Rumah</div>
                        <div class="text-gray-800">{{ $item->no_rumah ?? '-' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs text-gray-500 mb-1">Alamat</div>
                        <div class="text-gray-800 whitespace-pre-line">{{ $item->alamat ?? '-' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs text-gray-500 mb-1">Tampil di direktori</div>
                        <div class="text-gray-800">{{ $item->is_public ? 'Ya' : 'Tidak' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="homi-card space-y-3">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">Tindakan</div>

                <div class="flex flex-col gap-2">
                    <a href="{{ route('residents.edit', $item->id) }}"
                       class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white text-sm font-semibold text-center hover:bg-sky-800">
                        Edit
                    </a>

                    <a href="{{ route('residents.index') }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-center hover:bg-gray-50">
                        Kembali
                    </a>

                    <form action="{{ route('residents.destroy', $item->id) }}" method="POST"
                          onsubmit="return confirm('Hapus data warga dari direktori?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full px-4 py-2 rounded-xl border border-rose-200 text-rose-700 text-sm font-semibold hover:bg-rose-50">
                            Hapus dari Direktori
                        </button>
                    </form>
                </div>
            </div>

            @if(!$u)
                <div class="homi-card bg-rose-50 border-rose-200 text-sm text-rose-800">
                    Data ini tidak punya relasi user (user_id kosong / user terhapus). Hapus dari direktori agar tidak error.
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
