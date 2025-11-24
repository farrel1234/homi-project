@extends('layouts.app')

@section('title','Detail Pengajuan Surat')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Detail Pengajuan Surat</h1>
        <p class="homi-subtitle">
            Lihat detail pengajuan dan proses penerbitan surat PDF.
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

    <div class="grid md:grid-cols-3 gap-4">

        {{-- KIRI: INFO PENGAJU & DATA --}}
        <div class="md:col-span-2 space-y-4">

            {{-- Info pengaju --}}
            <div class="homi-card space-y-3">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Data Pengaju
                </div>

                <div class="grid md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Nama Warga</div>
                        <div class="text-gray-800">
                            {{ $item->user->full_name ?? $item->user->username ?? '-' }}
                        </div>
                        @if($item->user->email)
                            <div class="text-[11px] text-gray-500">
                                {{ $item->user->email }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Jenis Surat</div>
                        <div class="text-gray-800">
                            {{ $item->type->name ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-3 text-[11px] text-gray-500">
                    <div>Diajukan: {{ $item->created_at->format('d M Y H:i') }}</div>
                    @if($item->updated_at)
                        <div>Update terakhir: {{ $item->updated_at->format('d M Y H:i') }}</div>
                    @endif
                </div>
            </div>

            {{-- Data input dinamis --}}
            <div class="homi-card space-y-3">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Data yang Diisi Warga
                </div>

                @php
                    $dataInput = $item->data_input ?? [];
                @endphp

                @if(empty($dataInput))
                    <p class="text-sm text-gray-400">
                        Tidak ada data tambahan.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="homi-table text-xs text-left text-gray-800">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataInput as $key => $value)
                                    <tr>
                                        <td class="align-top">
                                            <span class="font-semibold">
                                                {{ Str::of($key)->replace('_',' ')->title() }}
                                            </span>
                                        </td>
                                        <td class="align-top">
                                            {{ $value }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Preview surat --}}
            <div class="homi-card space-y-3">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Preview Surat (Template Terisi)
                </div>
                <div class="border border-gray-200 rounded-lg bg-white max-h-[400px] overflow-auto p-4 text-sm leading-relaxed">
                    {!! $filledHtml !!}
                </div>
            </div>
        </div>

        {{-- KANAN: STATUS & AKSI --}}
        <div class="space-y-4">

            <div class="homi-card space-y-4">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Status & Tindakan
                </div>

                <div>
                    <div class="text-xs text-gray-500 mb-1">Status Saat Ini</div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $item->status_badge_class }}">
                        {{ $item->status_label }}
                    </span>
                </div>

                {{-- Tombol aksi --}}
                <div class="space-y-2">
                    @if($item->status !== 'approved')
                        <form method="POST" action="{{ route('letter-requests.approve', $item->id) }}">
                            @csrf
                            <button
                                class="w-full inline-flex justify-center items-center px-3 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">
                                Setujui & Generate PDF
                            </button>
                        </form>
                    @endif

                    @if($item->status !== 'rejected')
                        <form method="POST" action="{{ route('letter-requests.reject', $item->id) }}"
                              onsubmit="return confirm('Yakin tolak pengajuan surat ini?')">
                            @csrf
                            <button
                                class="w-full inline-flex justify-center items-center px-3 py-2 rounded-xl bg-rose-500 text-white text-sm font-semibold hover:bg-rose-600">
                                Tolak Pengajuan
                            </button>
                        </form>
                    @endif

                    @if($item->pdf_path)
                        <a href="{{ route('letter-requests.download', $item->id) }}"
                           class="w-full inline-flex justify-center items-center px-3 py-2 rounded-xl bg-[var(--homi-blue)] text-white text-sm font-semibold hover:bg-sky-800 mt-1">
                            Download PDF
                        </a>
                    @endif
                </div>

                <a href="{{ route('letter-requests.index') }}"
                   class="block text-center text-[12px] text-gray-500 hover:underline mt-2">
                    &larr; Kembali ke daftar pengajuan
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
