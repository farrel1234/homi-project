@extends('layouts.app')

@section('title','Kirim Notifikasi')

@php
    // Label period supaya orang awam ngerti (contoh: 2026-06 => Juni 2026)
    $periodLabel = null;
    $oldPeriod = old('period');
    if ($oldPeriod) {
        try {
            $periodLabel = \Carbon\Carbon::createFromFormat('Y-m', $oldPeriod)->translatedFormat('F Y');
        } catch (\Throwable $e) {
            $periodLabel = null;
        }
    }
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="homi-card">
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="homi-title">Kirim Notifikasi</div>
                <div class="homi-subtitle">Notifikasi akan muncul di aplikasi warga (in-app).</div>
            </div>

            <a href="{{ route('admin.notifications.index') }}"
               class="px-4 py-2 rounded-lg border border-[var(--homi-border)] text-sm hover:bg-gray-50">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="mt-4 p-3 rounded-lg bg-rose-50 text-rose-700 text-sm border border-rose-100">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.notifications.store') }}" class="mt-6 space-y-4">
            @csrf

            {{-- Pilih Warga --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Warga</label>
                <select name="user_id"
                        class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-sky-200">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>
                            {{ $u->full_name ?? $u->name ?? $u->username ?? '-' }} — {{ $u->email ?? '-' }}
                        </option>
                    @endforeach
                </select>
                <div class="text-[12px] text-gray-500 mt-1">
                    Pilih warga yang akan menerima notifikasi.
                </div>
            </div>

            {{-- Judul + Kategori --}}
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Notifikasi</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           placeholder="Contoh: Tagihan iuran bulan Juni sudah terbit"
                           class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-sky-200"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                    {{-- tetap kirim ke field 'type' biar backend aman --}}
                    <select name="type"
                            class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-sky-200">
                        @php $t = old('type', 'general'); @endphp
                        <option value="general"  @selected($t==='general')>Umum</option>
                        <option value="invoice"  @selected($t==='invoice')>Tagihan Iuran</option>
                        <option value="announcement" @selected($t==='announcement')>Pengumuman</option>
                        <option value="complaint" @selected($t==='complaint')>Pengaduan</option>
                    </select>
                    <div class="text-[12px] text-gray-500 mt-1">
                        Kategori membantu aplikasi mengelompokkan notifikasi.
                    </div>
                </div>
            </div>

            {{-- Pesan --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Pesan</label>
                <textarea name="message" rows="5"
                          placeholder="Tulis pesan singkat dan jelas..."
                          class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-sky-200"
                          required>{{ old('message') }}</textarea>
            </div>

            {{-- Tujuan + Info Tagihan --}}
            <div class="grid md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tujuan Saat Dibuka (opsional)</label>
                    {{-- tetap pakai field name="route" --}}
                    <select name="route"
                            class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-sky-200">
                        @php $r = old('route', 'TagihanIuran'); @endphp
                        <option value="" @selected($r==='')>Tidak ada (hanya tampilkan notifikasi)</option>
                        <option value="TagihanIuran" @selected($r==='TagihanIuran')>Buka halaman Tagihan Iuran</option>
                        <option value="PengaduanWarga" @selected($r==='PengaduanWarga')>Buka halaman Pengaduan</option>
                        <option value="Beranda" @selected($r==='Beranda')>Buka Beranda</option>
                    </select>
                    <div class="text-[12px] text-gray-500 mt-1">
                        Jika diklik, warga diarahkan ke halaman tertentu.
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan Tagihan (opsional)</label>
                    {{-- tetap kirim ke field 'period' --}}
                    <input type="text" name="period" value="{{ old('period') }}"
                           placeholder="Contoh: 2026-06"
                           class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm">
                    <div class="text-[12px] text-gray-500 mt-1">
                        Format: <span class="font-mono">YYYY-MM</span>
                        @if($periodLabel)
                            · Terbaca: <span class="font-semibold text-gray-700">{{ $periodLabel }}</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Tagihan (opsional)</label>
                    {{-- tetap kirim ke field 'invoice_id' --}}
                    <input type="number" name="invoice_id" value="{{ old('invoice_id') }}"
                           placeholder="Contoh: 123"
                           class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm">
                    <div class="text-[12px] text-gray-500 mt-1">
                        Kalau tahu nomor tagihannya. Kalau tidak, boleh dikosongkan.
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.notifications.index') }}"
                   class="px-4 py-2 rounded-lg border border-[var(--homi-border)] text-sm hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-sm font-semibold hover:bg-orange-500">
                    Kirim
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
