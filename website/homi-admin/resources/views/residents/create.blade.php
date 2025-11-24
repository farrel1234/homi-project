@extends('layouts.app')

@section('title', 'Tambah Warga')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Tambah Data Warga</h1>
            <p class="text-xs text-gray-500">Hubungkan akun user dengan data warga.</p>
        </div>
        <a href="{{ route('residents.index') }}"
           class="text-xs text-gray-500 hover:underline">
            &larr; Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-md bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('residents.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
        @csrf

        {{-- User --}}
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Akun User <span class="text-red-500">*</span>
            </label>
            <select name="user_id"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                <option value="">-- Pilih User Warga --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                        {{ $user->full_name ?? $user->username }} — {{ $user->email }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nomor Rumah --}}
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Nomor Rumah / Blok
            </label>
            <input type="text" name="house_number" value="{{ old('house_number') }}"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Contoh: A-01 / B-12">
        </div>

        {{-- Alamat --}}
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Alamat
            </label>
            <textarea name="address" rows="3"
                      class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                      placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
        </div>

        {{-- NIK / ID Number --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Nomor Identitas (KTP / KK)
                </label>
                <input type="text" name="id_number" value="{{ old('id_number') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            {{-- Kepala Keluarga --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Nama Kepala Keluarga
                </label>
                <input type="text" name="family_head" value="{{ old('family_head') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        {{-- Info Lain --}}
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Informasi Tambahan
            </label>
            <textarea name="other_info" rows="3"
                      class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                      placeholder="Catatan lain (mis. jumlah anggota keluarga, catatan khusus)...">{{ old('other_info') }}</textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('residents.index') }}"
               class="px-3 py-2 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 rounded-lg text-xs font-medium text-white bg-blue-600 hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
