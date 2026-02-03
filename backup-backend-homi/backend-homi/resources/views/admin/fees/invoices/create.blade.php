@extends('layouts.app')

@section('title', 'Buat Tagihan Iuran')

@section('content')
<div class="homi-card">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="homi-title">Buat Tagihan Iuran</div>
            <div class="homi-subtitle">Pilih periode bulan mulai–sampai. Sistem akan membuat tagihan per bulan.</div>
        </div>

        <a href="{{ route('admin.fees.invoices.index') }}"
           class="px-4 py-2 rounded-lg border border-gray-200 text-sm hover:bg-gray-50">
            Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="mt-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mt-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4"
          method="POST"
          action="{{ route('admin.fees.invoices.store') }}">
        @csrf

        <div>
            <label class="text-sm font-medium text-gray-700">Jenis Iuran</label>
            <select name="fee_type_id" class="mt-1 w-full border rounded-lg px-3 py-2">
                @foreach($feeTypes as $t)
                    <option value="{{ $t->id }}">{{ $t->name ?? ('FeeType #'.$t->id) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Nominal (Rp)</label>
            <input type="number" name="amount" min="1"
                   class="mt-1 w-full border rounded-lg px-3 py-2"
                   value="{{ old('amount', 50000) }}">
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Tahun</label>
            <input type="number" name="tahun" min="2000" max="2100"
                   class="mt-1 w-full border rounded-lg px-3 py-2"
                   value="{{ old('tahun', date('Y')) }}">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-sm font-medium text-gray-700">Bulan Mulai</label>
                <select name="bulan_mulai" class="mt-1 w-full border rounded-lg px-3 py-2">
                    @foreach($monthNames as $num => $label)
                        <option value="{{ $num }}" {{ (int)old('bulan_mulai', 1) === $num ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Bulan Sampai</label>
                <select name="bulan_sampai" class="mt-1 w-full border rounded-lg px-3 py-2">
                    @foreach($monthNames as $num => $label)
                        <option value="{{ $num }}" {{ (int)old('bulan_sampai', 12) === $num ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Target</label>
            <select name="target" id="target" class="mt-1 w-full border rounded-lg px-3 py-2">
                <option value="all" {{ old('target','all')==='all' ? 'selected' : '' }}>Semua Warga</option>
                <option value="one" {{ old('target')==='one' ? 'selected' : '' }}>Satu Warga</option>
            </select>
        </div>

        <div id="userWrap" class="{{ old('target')==='one' ? '' : 'hidden' }}">
            <label class="text-sm font-medium text-gray-700">Pilih Warga</label>
            <select name="user_id" class="mt-1 w-full border rounded-lg px-3 py-2">
                <option value="">-- pilih --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ (string)old('user_id')===(string)$u->id ? 'selected' : '' }}>
                        {{ $u->full_name ?? $u->name ?? ('User #'.$u->id) }} (ID: {{ $u->id }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2 mt-2 flex items-center gap-3">
            <button class="px-5 py-2 rounded-lg bg-[var(--homi-orange)] text-white hover:bg-orange-500">
                Buat Tagihan
            </button>

            @if(isset($qr) && $qr && $qr->display_url)
                <span class="text-xs text-gray-500">
                    QR aktif terdeteksi ✅
                </span>
            @else
                <span class="text-xs text-red-600">
                    QR aktif belum ada ⚠️
                </span>
            @endif
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const target = document.getElementById('target');
    const wrap   = document.getElementById('userWrap');
    function sync() {
        if (target.value === 'one') wrap.classList.remove('hidden');
        else wrap.classList.add('hidden');
    }
    target.addEventListener('change', sync);
    sync();
});
</script>
@endpush
@endsection
