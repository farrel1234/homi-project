@extends('layouts.app')

@section('title','Detail Pengajuan')

@section('content')
<div class="space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="homi-title">Detail Pengajuan</h1>
            <p class="homi-subtitle">ID: {{ $item->id }} — {{ $item->type->name ?? '-' }}</p>
        </div>

        {{-- ✅ balik ke route yang BENAR --}}
        <a href="{{ route('service-requests.index') }}" class="text-sm text-gray-500 hover:underline">Kembali</a>
    </div>

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

    <div class="homi-card space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-xs text-gray-500">Warga</div>
                <div class="font-medium">
                    {{ $item->user->full_name ?? $item->user->name ?? $item->user->username ?? '-' }}
                </div>
                <div class="text-xs text-gray-500">{{ $item->user->email ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs text-gray-500">Status</div>
                <div class="font-semibold">{{ $item->status_label ?? $item->status }}</div>
                <div class="text-xs text-gray-500">
                    Diverifikasi: {{ $item->verified_at ? $item->verified_at->format('d M Y H:i') : '-' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-500">Lokasi</div>
                <div class="font-medium">{{ $item->place ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs text-gray-500">Tanggal Pengajuan</div>
                <div class="font-medium">{{ $item->request_date ? $item->request_date->format('d M Y') : '-' }}</div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs text-gray-500">Subjek</div>
                <div class="font-medium">{{ $item->subject ?? $item->title ?? '-' }}</div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs text-gray-500">Catatan Admin</div>
                <div class="font-medium">{{ $item->admin_note ?? '-' }}</div>
            </div>
        </div>

        <hr>

        <div>
            <div class="text-sm font-semibold text-gray-800 mb-2">Data Input (untuk Surat)</div>
            @if(isset($dataInput) && count($dataInput))
                <div class="overflow-x-auto">
                    <table class="homi-table text-sm">
                        <thead>
                            <tr><th>Field</th><th>Value</th></tr>
                        </thead>
                        <tbody>
                            @foreach($dataInput as $k => $v)
                                <tr>
                                    <td class="font-medium">{{ $k }}</td>
                                    <td>{{ is_array($v) ? json_encode($v) : $v }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-sm text-gray-500">Tidak ada data_input.</div>
            @endif
        </div>

        <div class="flex flex-wrap gap-2 pt-2">
            @if($item->status !== 'approved')
                <form method="POST" action="{{ route('service-requests.approve', $item->id) }}">
                    @csrf
                    <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-medium">
                        Approve & Generate PDF
                    </button>
                </form>
            @endif

            @if($item->status !== 'rejected')
                <form method="POST" action="{{ route('service-requests.reject', $item->id) }}">
                    @csrf
                    <button class="px-4 py-2 rounded-xl bg-rose-600 text-white hover:bg-rose-700 text-sm font-medium">
                        Reject
                    </button>
                </form>
            @endif

            @if($item->pdf_path)
                <a href="{{ route('service-requests.download', $item->id) }}"
                   class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white hover:bg-sky-800 text-sm font-medium">
                    Download PDF
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
