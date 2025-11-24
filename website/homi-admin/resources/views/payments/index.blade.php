@extends('layouts.app')

@section('title', 'Pembayaran Iuran')

@section('content')
<div class="space-y-5">

    {{-- Judul Halaman --}}
    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Pembayaran Iuran Warga</h1>
        <p class="homi-subtitle">
            Halaman ini untuk mengecek dan memproses pembayaran iuran warga perumahan.
        </p>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="homi-card bg-emerald-50 border-emerald-200 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="homi-card bg-red-50 border-rose-200 text-sm text-rose-800">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Panel Utama: Filter + Aksi + Tabel --}}
    <div class="homi-card space-y-4">

        {{-- Bar Atas: Filter --}}
        <form method="GET" action="{{ route('payments.index') }}"
              class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-sm">

            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-gray-700 font-medium">Tampilkan:</span>

                <select name="status"
                        class="rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] text-sm">
                    <option value="">Semua pembayaran</option>
                    <option value="pending"   @selected($status === 'pending')>Belum Diproses (Pending)</option>
                    <option value="paid"      @selected($status === 'paid')>Sudah Dibayar (Paid)</option>
                    <option value="failed"    @selected($status === 'failed')>Ditolak / Gagal (Failed)</option>
                    <option value="cancelled" @selected($status === 'cancelled')>Dibatalkan</option>
                </select>

                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       placeholder="Cari nama warga / keterangan"
                       class="w-64 max-w-full rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] text-sm">

                <button type="submit"
                        class="px-3 py-2 rounded-xl bg-[var(--homi-blue)] text-white hover:bg-sky-800 text-sm font-medium">
                    Tampilkan
                </button>

                @if($q || $status)
                    <a href="{{ route('payments.index') }}"
                       class="text-xs text-gray-500 hover:underline">
                        Reset
                    </a>
                @endif
            </div>

            <div class="text-[12px] text-gray-500">
                Total data:
                <span class="font-semibold text-gray-700">{{ $payments->total() }}</span> pembayaran
            </div>
        </form>

        {{-- Bar Bawah: Aksi untuk data terpilih --}}
        <form method="POST" action="{{ route('payments.bulk') }}" class="space-y-3">
            @csrf

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-sm">
                <div class="text-gray-700 font-medium">
                    Aksi untuk data yang dipilih:
                </div>

                <div class="flex-1 flex flex-wrap items-center gap-2">
                    <input type="text"
                           name="reason"
                           placeholder="Catatan untuk admin (opsional)..."
                           class="w-full md:flex-1 rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] text-sm">

                    {{-- Dua tombol terpisah, bukan dropdown --}}
                    <div class="flex gap-2">
                        <button type="submit"
                                name="action"
                                value="approve"
                                class="px-4 py-2 rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 text-sm font-semibold">
                            Setujui Terpilih
                        </button>

                        <button type="submit"
                                name="action"
                                value="reject"
                                class="px-4 py-2 rounded-xl bg-rose-500 text-white hover:bg-rose-600 text-sm font-semibold">
                            Tolak Terpilih
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-800 border border-gray-300 rounded-xl overflow-hidden bg-white">
                    <thead class="bg-orange-50">
                        <tr class="text-xs uppercase tracking-wide text-gray-600">
                            <th class="px-3 py-3 border-b border-gray-300">
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" class="select-all rounded border-gray-300">
                                    <span class="text-[11px] font-medium text-gray-700">Pilih</span>
                                </label>
                            </th>
                            <th class="px-3 py-3 border-b border-gray-300">Nama Warga</th>
                            <th class="px-3 py-3 border-b border-gray-300">Keterangan</th>
                            <th class="px-3 py-3 border-b border-gray-300">Jumlah</th>
                            <th class="px-3 py-3 border-b border-gray-300">Jatuh Tempo</th>
                            <th class="px-3 py-3 border-b border-gray-300">Status</th>
                            <th class="px-3 py-3 border-b border-gray-300">Tanggal Dibayar</th>
                            <th class="px-3 py-3 border-b border-gray-300">Catatan Admin</th>
                            <th class="px-3 py-3 border-b border-gray-300 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="hover:bg-orange-50/60">
                                {{-- Checkbox --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200">
                                    <input type="checkbox"
                                           name="selected[]"
                                           value="{{ $payment->id }}"
                                           class="row-checkbox rounded border-gray-300">
                                </td>

                                {{-- Warga --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200">
                                    <div class="font-medium text-[13px] text-gray-900">
                                        {{ $payment->user->full_name ?? $payment->user->username ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $payment->user->email ?? '-' }}
                                    </div>
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200 text-[12px] text-gray-700">
                                    {{ $payment->description ?? '-' }}
                                </td>

                                {{-- Jumlah --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200 text-[13px] font-semibold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>

                                {{-- Jatuh Tempo --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200 text-[12px] text-gray-700 whitespace-nowrap">
                                    {{ optional($payment->due_date)->format('d M Y') ?? '-' }}
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200">
                                    @php
                                        $label = [
                                            'pending'   => 'Belum Diproses',
                                            'paid'      => 'Sudah Dibayar',
                                            'failed'    => 'Ditolak / Gagal',
                                            'cancelled' => 'Dibatalkan',
                                        ][$payment->status] ?? $payment->status;

                                        $badgeClass = match ($payment->status) {
                                            'pending'   => 'bg-amber-100 text-amber-800 border border-amber-200',
                                            'paid'      => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                            'failed'    => 'bg-rose-100 text-rose-800 border border-rose-200',
                                            'cancelled' => 'bg-gray-100 text-gray-700 border border-gray-200',
                                            default     => 'bg-gray-100 text-gray-700 border border-gray-200',
                                        };
                                    @endphp

                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium {{ $badgeClass }}">
                                        {{ $label }}
                                    </span>
                                </td>

                                {{-- Tanggal Dibayar --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200 text-[12px] text-gray-700 whitespace-nowrap">
                                    {{ optional($payment->paid_at)->format('d M Y H:i') ?? '-' }}
                                </td>

                                {{-- Catatan Admin --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200 text-[12px] text-gray-700">
                                    {{ $payment->admin_note ?? '-' }}
                                </td>

                                {{-- Aksi --}}
                                <td class="px-3 py-3 align-top border-t border-gray-200 text-right">
                                    <a href="{{ route('payments.show', $payment->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 rounded-xl bg-sky-50 text-[12px] text-[var(--homi-blue)] hover:bg-sky-100 font-medium">
                                        Lihat detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-sm text-gray-400 border-t border-gray-200">
                                    Belum ada data pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginate --}}
            <div class="pt-1">
                {{ $payments->links() }}
            </div>
        </form>
    </div>

    {{-- Keterangan Warna Status --}}
    <div class="homi-card text-[12px] text-gray-700 space-y-2">
        <p class="font-semibold text-gray-800">Keterangan status:</p>
        <div class="flex flex-wrap gap-3">
            <div class="inline-flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-amber-400"></span>
                <span><strong>Belum Diproses</strong> = Menunggu dicek oleh admin.</span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-emerald-500"></span>
                <span><strong>Sudah Dibayar</strong> = Pembayaran sudah dinyatakan lunas.</span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-rose-500"></span>
                <span><strong>Ditolak / Gagal</strong> = Bukti pembayaran tidak valid / ditolak.</span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-gray-400"></span>
                <span><strong>Dibatalkan</strong> = Transaksi dibatalkan.</span>
            </div>
        </div>
    </div>
</div>

{{-- Script kecil untuk "Pilih semua" --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.querySelector('.select-all');
        const checkboxes = document.querySelectorAll('.row-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
            });
        }
    });
</script>
@endpush

@endsection
