@extends('layouts.app')

@section('title','Laporan Pengaduan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Laporan Pengaduan</h1>
        <p class="homi-subtitle">
            Kelola laporan pengaduan yang dikirim oleh warga Hawai Garden.
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

    {{-- PANEL UTAMA --}}
    <div class="homi-card space-y-4">

        {{-- FILTER BAR --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">
                    Daftar Pengaduan Warga
                </h2>
                <p class="text-[12px] text-gray-500">
                    Filter berdasarkan status atau cari berdasarkan judul / deskripsi / nama pelapor.
                </p>
            </div>

            <form method="GET"
                  action="{{ route('complaints.index') }}"
                  class="flex flex-col md:flex-row gap-2 md:items-center text-sm">

                {{-- Filter Status --}}
                <select name="status"
                        class="rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                    <option value="">Semua status</option>
                    <option value="submitted"     @selected(request('status')=='submitted')>Diajukan</option>
                    <option value="investigating" @selected(request('status')=='investigating')>Diselidiki</option>
                    <option value="resolved"      @selected(request('status')=='resolved')>Selesai</option>
                    <option value="dismissed"     @selected(request('status')=='dismissed')>Ditolak</option>
                </select>

                {{-- Pencarian --}}
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari judul / deskripsi / nama pelapor..."
                    class="w-full md:w-72 rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]"
                >

                <button
                    class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white hover:bg-sky-800 font-medium">
                    Cari
                </button>

                @if(request('status') || request('q'))
                    <a href="{{ route('complaints.index') }}"
                       class="text-xs text-gray-500 hover:underline">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- TABEL --}}
        <div class="overflow-x-auto">
            <table class="homi-table text-sm text-left text-gray-800">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pelapor</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $c)
                        <tr>
                            {{-- Judul + deskripsi singkat --}}
                            <td class="align-top">
                                <div class="font-medium text-[13px] text-gray-900">
                                    {{ $c->title }}
                                </div>
                                @if($c->description)
                                    <p class="text-[11px] text-gray-500 line-clamp-2">
                                        {{ Str::limit($c->description, 80) }}
                                    </p>
                                @endif
                            </td>

                            {{-- Pelapor --}}
                            <td class="align-top">
                                <div class="text-[13px] text-gray-800">
                                    {{ $c->user->full_name ?? $c->user->username ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-500">
                                    {{ $c->user->email ?? '-' }}
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td class="align-top">
                                <span class="text-[13px]">
                                    {{ $c->category ?? '-' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="align-top">
                                @php
                                    $badgeClass = match($c->status) {
                                        'submitted'     => 'bg-gray-100 text-gray-700 border border-gray-200',
                                        'investigating' => 'bg-sky-50 text-sky-700 border border-sky-200',
                                        'resolved'      => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                        'dismissed'     => 'bg-rose-50 text-rose-700 border border-rose-200',
                                        default         => 'bg-gray-50 text-gray-600 border border-gray-200',
                                    };
                                    $statusLabel = match($c->status) {
                                        'submitted'     => 'Diajukan',
                                        'investigating' => 'Diselidiki',
                                        'resolved'      => 'Selesai',
                                        'dismissed'     => 'Ditolak',
                                        default         => '-',
                                    };
                                @endphp

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>

                                <div class="mt-1 text-[11px] text-gray-500">
                                    {{ $c->created_at->format('d M Y H:i') }}
                                </div>
                            </td>

                            {{-- Dibuat --}}
                            <td class="align-top text-[12px]">
                                {{ $c->created_at->format('d M Y H:i') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="align-top text-right whitespace-nowrap">
                                <a href="{{ route('complaints.edit', $c->id) }}"
                                   class="text-[12px] text-[var(--homi-blue)] hover:underline font-medium">
                                    Proses
                                </a>

                                <span class="mx-1 text-gray-300">|</span>

                                <form action="{{ route('complaints.destroy', $c->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Yakin hapus laporan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-[12px] text-rose-600 hover:underline font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-sm text-gray-400">
                                Belum ada laporan pengaduan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINASI --}}
        <div class="pt-1">
            {{ $items->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection
