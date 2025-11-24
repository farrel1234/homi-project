@extends('layouts.app')

@section('title', 'Edit Data Warga')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Judul --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Edit Data Warga</h1>
        <p class="text-sm text-gray-600">
            Perbarui informasi warga dengan benar. Mohon diisi sesuai data aslinya.
        </p>
    </div>

    {{-- Petunjuk --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-sm text-blue-900">
        <p class="font-semibold mb-1">Petunjuk pengisian:</p>
        <ul class="list-disc list-inside space-y-1 text-[13px]">
            <li>Pastikan memilih akun warga yang sesuai.</li>
            <li>Isi Nomor Rumah dengan format jelas (misal: A-01).</li>
            <li>Alamat & Kepala Keluarga boleh dikosongkan bila tidak ada.</li>
            <li>Klik <strong>Simpan Perubahan</strong> setelah selesai.</li>
        </ul>
    </div>

    {{-- Error --}}
    @if ($errors->any())
        <div class="rounded-md bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST"
          action="{{ route('residents.update', $resident) }}"
          class="bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- Pilih User --}}
        <div>
            <label class="block text-[14px] font-medium text-gray-800 mb-1">
                Pilih Akun Warga <span class="text-red-500">*</span>
            </label>
            <select name="user_id" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 text-[15px] focus:border-blue-600 focus:ring-blue-600">
                @foreach($users as $user)
                    <option value="{{ $user->id }}"
                        @selected(old('user_id', $resident->user_id) == $user->id)>
                        {{ $user->full_name ?? $user->username }} — {{ $user->email }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nomor Rumah --}}
        <div>
            <label class="block text-[14px] font-medium text-gray-800 mb-1">
                Nomor Rumah / Blok
            </label>
            <input type="text"
                   name="house_number"
                   value="{{ old('house_number', $resident->house_number) }}"
                   placeholder="Contoh: A-05"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 text-[15px] focus:border-blue-600 focus:ring-blue-600">
        </div>

        {{-- Alamat --}}
        <div>
            <label class="block text-[14px] font-medium text-gray-800 mb-1">
                Alamat Warga
            </label>
            <textarea name="address" rows="3"
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 text-[15px] focus:border-blue-600 focus:ring-blue-600"
                      placeholder="Alamat lengkap...">{{ old('address', $resident->address) }}</textarea>
        </div>

        {{-- KTP & Kepala Keluarga --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[14px] font-medium text-gray-800 mb-1">
                    Nomor Identitas (KTP / KK)
                </label>
                <input type="text"
                       name="id_number"
                       value="{{ old('id_number', $resident->id_number) }}"
                       placeholder="Nomor KTP / KK..."
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 text-[15px] focus:border-blue-600 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-[14px] font-medium text-gray-800 mb-1">
                    Nama Kepala Keluarga
                </label>
                <input type="text"
                       name="family_head"
                       value="{{ old('family_head', $resident->family_head) }}"
                       placeholder="Nama kepala keluarga..."
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 text-[15px] focus:border-blue-600 focus:ring-blue-600">
            </div>
        </div>

        {{-- Info Tambahan --}}
        <div>
            <label class="block text-[14px] font-medium text-gray-800 mb-1">
                Informasi Tambahan
            </label>
            <textarea name="other_info" rows="3"
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 text-[15px] focus:border-blue-600 focus:ring-blue-600"
                      placeholder="Catatan tambahan...">{{ old('other_info', $resident->other_info) }}</textarea>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-between pt-4">
            <a href="{{ route('residents.index') }}"
               class="px-5 py-2.5 rounded-lg bg-gray-200 text-gray-700 text-[14px] font-medium hover:bg-gray-300">
                Kembali
            </a>

            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-[14px] font-semibold hover:bg-blue-700">
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>
@endsection
