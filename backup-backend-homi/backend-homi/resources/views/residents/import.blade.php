@extends('layouts.app')

@section('title','Import CSV Warga')

@section('content')
<div class="space-y-6">

    <div>
        <div class="homi-title">Import CSV Warga</div>
        <div class="homi-subtitle">Upload file CSV untuk menambah data warga sekaligus.</div>
    </div>

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

    <div class="homi-card space-y-4">
        <div class="text-sm text-gray-700">
            Kolom yang didukung:
            <span class="block sm:inline mt-2 sm:mt-0 font-mono text-xs bg-gray-100 px-2 py-1 rounded break-words">
                full_name,email,phone,blok,no_rumah,is_public
            </span>
        </div>

        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2">
            <a href="{{ route('residents.template') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold hover:bg-gray-50">
                Download Template
            </a>

            <a href="{{ route('residents.index') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-gray-200 text-sm hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('residents.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">File CSV</label>
                <input type="file" name="file" accept=".csv,text/csv"
                       class="block w-full rounded-xl">
                <div class="text-[12px] text-gray-500 mt-1">
                    Maks 2MB. Baris dengan email yang sudah ada akan dilewati.
                </div>
            </div>

            <button
                class="w-full sm:w-auto px-4 py-2 rounded-lg bg-[var(--homi-blue)] text-white text-sm font-semibold hover:opacity-95">
                Import
            </button>
        </form>
    </div>

</div>
@endsection
