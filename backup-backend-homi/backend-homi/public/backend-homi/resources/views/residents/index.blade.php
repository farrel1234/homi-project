@extends('layouts.app')

@section('title','Data Warga')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Data Warga</h1>
        <p class="homi-subtitle">
            Kelola data warga yang ada di direktori Hawai Garden.
        </p>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('ok'))
        <div class="homi-card bg-emerald-50 border-emerald-200 text-sm text-emerald-800">
            {{ session('ok') }}
        </div>
    @endif
    @if(session('error'))
        <div class="homi-card bg-rose-50 border-rose-200 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="homi-card bg-rose-50 border-rose-200 text-sm text-rose-800">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="homi-card space-y-4">

        {{-- TOP BAR --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Daftar Warga</h2>
                <p class="text-[12px] text-gray-500">
                    Cari warga berdasarkan nama/email/username atau nomor rumah.
                </p>
            </div>

            <div class="flex flex-col md:flex-row gap-2 md:items-center">
                {{-- SEARCH --}}
                <form method="GET" action="{{ route('residents.index') }}" class="flex gap-2 items-center">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari..."
                        class="w-full md:w-72 rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]"
                    >

                    <button class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white hover:bg-sky-800 font-medium">
                        Cari
                    </button>

                    @if(request('q'))
                        <a href="{{ route('residents.index') }}" class="text-xs text-gray-500 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>

                {{-- BUTTON: TAMBAH WARGA --}}
                @if (Route::has('residents.create'))
                    <a href="{{ route('residents.create') }}"
                       class="px-4 py-2 rounded-xl border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100 font-semibold text-sm">
                        + Tambah Warga
                    </a>
                @endif
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="homi-table text-sm text-left text-gray-800">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>No Rumah</th>
                        <th>Blok</th>
                        <th>Publik</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items ?? $residents ?? [] as $r)
                        @php
                            $u = optional($r->user);
                            $isPublic = (bool) ($r->is_public ?? false);
                        @endphp
                        <tr>
                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <div class="font-medium text-[13px] text-gray-900">
                                    {{ $u->full_name ?? $u->name ?? $u->username ?? '-' }}
                                </div>
                            </td>

                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <span class="text-[13px] text-gray-800">
                                    {{ $u->email ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <span class="text-[13px] text-gray-800">
                                    {{ $u->username ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <span class="text-[13px] text-gray-800">
                                    {{ $r->house_number ?? $r->no_rumah ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <span class="text-[13px] text-gray-800">
                                    {{ $r->block ?? $r->blok ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                @if($isPublic)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                        Tidak
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-3 border-t border-gray-200 text-right whitespace-nowrap">
                                <a href="{{ route('residents.edit', $r->id) }}" class="text-gray-600 hover:underline">
                                    Edit
                                </a>

                                <span class="text-gray-300 mx-2">|</span>

                                <form action="{{ route('residents.destroy', $r->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Hapus data warga dari direktori?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-sm text-gray-400">
                                Belum ada data warga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="pt-1">
            {{ ($items ?? $residents)->withQueryString()->links() ?? '' }}
        </div>
    </div>

</div>
@endsection
