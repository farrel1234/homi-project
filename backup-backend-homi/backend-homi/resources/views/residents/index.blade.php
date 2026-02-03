@extends('layouts.app')

@section('title','Data Warga')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <div class="homi-title">Data Warga</div>
        <div class="homi-subtitle">
            Kelola data warga yang ada di direktori Hawai Garden.
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('ok'))
        <div class="p-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm border border-emerald-100">
            {{ session('ok') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 rounded-lg bg-rose-50 text-rose-700 text-sm border border-rose-100">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-3 rounded-lg bg-rose-50 text-rose-700 text-sm border border-rose-100">
            {{ $errors->first() }}
        </div>
    @endif

    @php
        $data = $items ?? $residents ?? null;
    @endphp

    <div class="homi-card w-full">

        {{-- TOP BAR --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-800">Daftar Warga</div>
                <div class="text-[12px] text-gray-500">
                    Cari warga berdasarkan nama/email/username atau nomor rumah.
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-center w-full lg:w-auto">

                {{-- SEARCH --}}
                <form method="GET" action="{{ route('residents.index') }}"
                      class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari..."
                        class="w-full sm:w-64 border border-[var(--homi-border)] rounded-full px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-sky-200"
                    >

                    <button
                        type="submit"
                        class="w-full sm:w-auto px-4 py-2 rounded-full bg-[var(--homi-blue)] text-white text-sm font-semibold hover:opacity-95">
                        Cari
                    </button>

                    @if(request('q'))
                        <a href="{{ route('residents.index') }}"
                           class="text-xs text-gray-500 hover:underline self-center sm:self-auto text-center w-full sm:w-auto">
                            Reset
                        </a>
                    @endif
                </form>

                {{-- IMPORT CSV --}}
                @if (Route::has('residents.import.form'))
                    <a href="{{ route('residents.import.form') }}"
                       class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold hover:bg-gray-50">
                        Import CSV
                    </a>
                @endif

                {{-- TAMBAH WARGA --}}
                @if (Route::has('residents.create'))
                    <a href="{{ route('residents.create') }}"
                       class="w-full sm:w-auto text-center px-4 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-sm font-semibold hover:bg-orange-500">
                        + Tambah
                    </a>
                @endif
            </div>
        </div>

        {{-- ===== MOBILE: CARD LIST ===== --}}
        <div class="mt-4 space-y-3 md:hidden">
            @forelse(($data ?? []) as $r)
                @php
                    $u = optional($r->user);
                    $isPublic = (bool) ($r->is_public ?? false);
                    $nama = $u->full_name ?? $u->name ?? $u->username ?? '-';
                    $email = $u->email ?? '-';
                    $username = $u->username ?? '-';
                    $noRumah = $r->house_number ?? $r->no_rumah ?? '-';
                    $blok = $r->block ?? $r->blok ?? '-';
                @endphp

                <div class="rounded-xl border border-[var(--homi-border)] bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 break-words">
                                {{ $nama }}
                            </div>
                            <div class="mt-1 text-xs text-gray-600 break-words">
                                {{ $email }}
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if($isPublic)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold
                                             bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Publik
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold
                                             bg-gray-100 text-gray-700 border border-gray-200">
                                    Private
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                        <div class="min-w-0">
                            <div class="text-[11px] text-gray-500">Username</div>
                            <div class="text-gray-800 break-words">{{ $username }}</div>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] text-gray-500">No Rumah</div>
                            <div class="text-gray-800">{{ $noRumah }}</div>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] text-gray-500">Blok</div>
                            <div class="text-gray-800">{{ $blok }}</div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('residents.edit', $r->id) }}"
                           class="w-full sm:w-auto text-center px-3 py-2 rounded-lg text-xs font-semibold border border-sky-200 text-sky-700 hover:bg-sky-50">
                            Edit
                        </a>

                        <form action="{{ route('residents.destroy', $r->id) }}"
                              method="POST"
                              class="w-full sm:w-auto"
                              onsubmit="return confirm('Hapus data warga dari direktori?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full sm:w-auto text-center px-3 py-2 rounded-lg text-xs font-semibold border border-rose-200 text-rose-700 hover:bg-rose-50">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-gray-500">
                    Belum ada data warga.
                </div>
            @endforelse
        </div>

        {{-- ===== TABLET/DESKTOP: TABLE ===== --}}
        <div class="mt-4 w-full overflow-x-auto hidden md:block">
            <table class="homi-table min-w-[980px] w-full table-auto">
                <thead>
                    <tr>
                        <th class="text-left">Nama</th>
                        <th class="text-left">Email</th>
                        <th class="text-left">Username</th>
                        <th class="text-left">No Rumah</th>
                        <th class="text-left">Blok</th>
                        <th class="text-left">Publik</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($data ?? []) as $r)
                        @php
                            $u = optional($r->user);
                            $isPublic = (bool) ($r->is_public ?? false);
                        @endphp

                        <tr>
                            <td>
                                <div class="font-medium text-gray-900 text-sm max-w-[260px] truncate">
                                    {{ $u->full_name ?? $u->name ?? $u->username ?? '-' }}
                                </div>
                            </td>

                            <td class="text-sm text-gray-700">
                                <div class="max-w-[260px] truncate">{{ $u->email ?? '-' }}</div>
                            </td>

                            <td class="text-sm text-gray-700">
                                <div class="max-w-[180px] truncate">{{ $u->username ?? '-' }}</div>
                            </td>

                            <td class="text-sm text-gray-700">
                                {{ $r->house_number ?? $r->no_rumah ?? '-' }}
                            </td>

                            <td class="text-sm text-gray-700">
                                {{ $r->block ?? $r->blok ?? '-' }}
                            </td>

                            <td>
                                @if($isPublic)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold
                                                 bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold
                                                 bg-gray-100 text-gray-700 border border-gray-200">
                                        Tidak
                                    </span>
                                @endif
                            </td>

                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('residents.edit', $r->id) }}"
                                   class="px-3 py-2 rounded-lg text-xs font-semibold border border-sky-200 text-sky-700 hover:bg-sky-50">
                                    Edit
                                </a>

                                <form action="{{ route('residents.destroy', $r->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Hapus data warga dari direktori?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="ml-2 px-3 py-2 rounded-lg text-xs font-semibold border border-rose-200 text-rose-700 hover:bg-rose-50">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                Belum ada data warga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($data && method_exists($data, 'links'))
            <div class="mt-4">
                {{ $data->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
