@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                Detail Pembayaran Iuran
            </h1>
            <p class="text-sm text-gray-600">
                Halaman ini menampilkan detail lengkap pembayaran yang dipilih.
            </p>
        </div>

        <a href="{{ route('payments.index') }}"
           class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">
            &larr; Kembali ke daftar
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-md bg-emerald-50 border border-emerald-200 px-3 py-2 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-md bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-800">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KIRI: DETAIL PEMBAYARAN --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- CARD DETAIL --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">ID Pembayaran</p>
                        <p class="text-sm font-mono text-gray-800">#{{ $payment->id }}</p>
                    </div>

                    @php
                        // status sekarang: pending / approved / rejected
                        $label = [
                            'pending'  => 'Belum Diproses',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ][$payment->status] ?? $payment->status;

                        $badgeClass = match($payment->status) {
                            'pending'  => 'bg-amber-100 text-amber-800',
                            'approved' => 'bg-emerald-100 text-emerald-800',
                            'rejected' => 'bg-rose-100 text-rose-800',
                            default    => 'bg-gray-100 text-gray-700',
                        };

                        // ambil invoice (kalau ada)
                        $invoice = $payment->invoice ?? null;
                        $amount  = $invoice->amount ?? null;
                        $dueDate = $invoice->due_date ?? null;
                    @endphp

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium {{ $badgeClass }}">
                        {{ $label }}
                    </span>
                </div>

                <div class="border-t border-gray-100 pt-3 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Nama Warga</p>
                        <p class="font-medium text-gray-900">
                            {{ $payment->user->name ?? '-' }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $payment->user->email ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Catatan / Deskripsi</p>
                        <p class="text-gray-800">{{ $payment->description ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Jumlah</p>
                        <p class="text-base font-semibold text-gray-900">
                            @if(!is_null($amount))
                                Rp {{ number_format($amount, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Jatuh Tempo</p>
                        <p class="text-gray-800">
                            {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d M Y') : '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Tanggal Review</p>
                        <p class="text-gray-800">
                            {{ optional($payment->paid_at)->format('d M Y H:i') ?? '-' }}
                        </p>
                        <p class="text-[11px] text-gray-400">
                            (paid_at = reviewed_at)
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Dibuat Pada</p>
                        <p class="text-gray-800">
                            {{ optional($payment->created_at)->format('d M Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
                        <p class="text-gray-800">
                            {{ optional($payment->updated_at)->format('d M Y H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- BUKTI PEMBAYARAN --}}
            @php
                $proof = $payment->proof_path ?? null;
                $proofUrl = null;

                if ($proof) {
                    if (preg_match('/^https?:\\/\\//i', $proof)) {
                        $proofUrl = $proof;
                    } else {
                        $proofUrl = asset('storage/' . ltrim($proof, '/'));
                    }
                }
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-800">Bukti Pembayaran</h3>

                @if($proofUrl)
                    <div class="space-y-2">
                        <div class="border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center max-h-80">
                            <img src="{{ $proofUrl }}" alt="Bukti pembayaran"
                                 class="object-contain max-h-80 w-full">
                        </div>

                        <a href="{{ $proofUrl }}" target="_blank"
                           class="inline-flex items-center text-xs text-sky-700 hover:underline">
                            Buka bukti pembayaran di tab baru
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500">
                        Belum ada bukti pembayaran yang tersimpan untuk transaksi ini.
                    </p>
                @endif
            </div>

            {{-- CATATAN ADMIN --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-2">
                <h3 class="text-sm font-semibold text-gray-800">Catatan Admin</h3>
                <p class="text-sm text-gray-700">
                    {{ $payment->admin_note ?? 'Belum ada catatan.' }}
                </p>
            </div>
        </div>

        {{-- KANAN: AKSI APPROVAL --}}
        <div class="space-y-4">

            {{-- DATA WARGA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-2">
                <h3 class="text-sm font-semibold text-gray-800 mb-1">Data Warga</h3>

                <div class="text-sm space-y-1">
                    <p>
                        <span class="text-gray-500 text-xs">Nama</span><br>
                        <span class="text-gray-900 font-medium">{{ $payment->user->name ?? '-' }}</span>
                    </p>
                    <p>
                        <span class="text-gray-500 text-xs">Email</span><br>
                        <span class="text-gray-800">{{ $payment->user->email ?? '-' }}</span>
                    </p>
                    <p>
                        <span class="text-gray-500 text-xs">No. HP</span><br>
                        <span class="text-gray-800">{{ $payment->user->phone ?? '-' }}</span>
                    </p>
                </div>
            </div>

            {{-- FORM APPROVAL --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-800">Aksi Approval</h3>

                <p class="text-xs text-gray-500">
                    Gunakan form di bawah untuk menyetujui atau menolak pembayaran ini.
                </p>

                {{-- SETUJUI --}}
                <form method="POST" action="{{ route('payments.approve', $payment) }}" class="space-y-2">
                    @csrf
                    <label class="block text-xs font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                    <textarea name="reason" rows="2"
                              class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                              placeholder="Contoh: Bukti sesuai, pembayaran disetujui."></textarea>

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                        Setujui Pembayaran
                    </button>
                </form>

                {{-- TOLAK --}}
                <form method="POST" action="{{ route('payments.reject', $payment) }}" class="space-y-2 pt-2 border-t border-gray-100">
                    @csrf
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alasan penolakan</label>
                    <textarea name="reason" rows="2"
                              class="w-full rounded-lg border-gray-300 focus:border-rose-500 focus:ring-rose-500 text-sm"
                              placeholder="Contoh: Bukti tidak jelas, silakan upload ulang."></textarea>

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 rounded-lg bg-rose-600 text-white text-sm font-medium hover:bg-rose-700">
                        Tolak Pembayaran
                    </button>
                </form>

                <p class="text-[11px] text-gray-400 pt-1">
                    Status akan tersimpan di sistem (review_status).
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
