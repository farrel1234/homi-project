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
    @if($errors->any())
        <div class="homi-card bg-rose-50 border-rose-200 text-sm text-rose-800">
            {{ $errors->first() }}
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
                    Filter berdasarkan status atau cari berdasarkan perihal / nama pelapor.
                </p>
            </div>

            <form method="GET"
                  action="{{ route('complaints.index') }}"
                  class="flex flex-col md:flex-row gap-2 md:items-center text-sm">

                {{-- Filter Status (ENUM DB: baru/diproses/selesai) --}}
                <select name="status"
                        class="rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                    <option value="">Semua status</option>
                    <option value="baru"     @selected(request('status')=='baru')>Baru</option>
                    <option value="diproses" @selected(request('status')=='diproses')>Diproses</option>
                    <option value="selesai"  @selected(request('status')=='selesai')>Selesai</option>
                </select>

                {{-- Pencarian --}}
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari perihal / nama pelapor..."
                    class="w-full md:w-72 rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]"
                >

                <button class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white hover:bg-sky-800 font-medium">
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
                        <th>Perihal</th>
                        <th>Pelapor</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $c)
                        <tr>
                            <td class="align-top">
                                <div class="font-medium text-[13px] text-gray-900">
                                    {{ $c->perihal ?? '-' }}
                                </div>
                            </td>

                            <td class="align-top">
                                <div class="text-[13px] text-gray-800">
                                    {{ $c->nama_pelapor ?? ($c->user->full_name ?? $c->user->username ?? '-') }}
                                </div>

                                @php $email = $c->user->email ?? null; @endphp
                                @if($email)
                                    <div class="text-[11px] text-gray-500">
                                        {{ $email }}
                                    </div>
                                @endif
                            </td>

                            <td class="align-top">
                                @php
                                    $badgeClass = match($c->status) {
                                        'baru'     => 'bg-gray-100 text-gray-700 border border-gray-200',
                                        'diproses' => 'bg-sky-50 text-sky-700 border border-sky-200',
                                        'selesai'  => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                        default    => 'bg-gray-50 text-gray-600 border border-gray-200',
                                    };

                                    $statusLabel = match($c->status) {
                                        'baru'     => 'Baru',
                                        'diproses' => 'Diproses',
                                        'selesai'  => 'Selesai',
                                        default    => '-',
                                    };
                                @endphp

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td class="align-top text-[12px]">
                                {{ optional($c->created_at)->format('d M Y H:i') ?? '-' }}
                            </td>

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
                            <td colspan="5" class="text-center text-sm text-gray-400">
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
