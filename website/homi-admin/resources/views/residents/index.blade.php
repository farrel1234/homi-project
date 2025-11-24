@extends('layouts.app')

@section('title','Data Warga')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Data Warga</h1>
        <p class="homi-subtitle">
            Kelola data warga perumahan Hawai Garden yang terdaftar di sistem HOMI.
        </p>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('ok'))
        <div class="homi-card bg-emerald-50 border-emerald-200 text-sm text-emerald-800">
            {{ session('ok') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="homi-card bg-rose-50 border-rose-200 text-sm text-rose-800">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PANEL: FILTER + TABEL DATA WARGA --}}
    <div class="homi-card space-y-4">

    
        {{-- BAR ATAS: PENCARIAN --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">
                    Daftar Warga Terdaftar
                </h2>
                <p class="text-[12px] text-gray-500">
                    Cari warga berdasarkan nama, email, username, atau nomor rumah.
                </p>
            </div>

            <div class="flex-1">
                <form method="GET" action="{{ route('residents.index') }}"
                      class="flex flex-col md:flex-row gap-2 md:items-center md:justify-end text-sm">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Cari nama/email/username/no. rumah..."
                        class="w-full md:w-72 rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] px-3 py-2"
                    >
                    <button
                        class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white hover:bg-sky-800 font-medium">
                        Cari
                    </button>
                    @if($q)
                        <a href="{{ route('residents.index') }}"
                           class="text-xs text-gray-500 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- TABEL DATA WARGA --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-800 border border-gray-300 rounded-xl overflow-hidden bg-white">
                <thead class="bg-orange-50">
                    <tr class="text-xs uppercase tracking-wide text-gray-600">
                        <th class="px-6 py-3 border-b border-gray-300">Nama Lengkap</th>
                        <th class="px-6 py-3 border-b border-gray-300">Email</th>
                        <th class="px-6 py-3 border-b border-gray-300">Username</th>
                        <th class="px-6 py-3 border-b border-gray-300">No. Rumah</th>
                        <th class="px-6 py-3 border-b border-gray-300">Role</th>
                        <th class="px-6 py-3 border-b border-gray-300 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $r)
                        <tr class="hover:bg-orange-50/60">
                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <div class="font-medium text-[13px] text-gray-900">
                                    {{ $r->user->full_name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <span class="text-[13px] text-gray-800">
                                    {{ $r->user->email }}
                                </span>
                            </td>
                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <span class="text-[13px] text-gray-800">
                                    {{ $r->user->username }}
                                </span>
                            </td>
                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <span class="text-[13px] text-gray-800">
                                    {{ $r->house_number ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 align-top border-t border-gray-200">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium
                                             bg-sky-50 text-sky-800 border border-sky-200">
                                    {{ $r->user->role->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 align-top border-t border-gray-200 text-right whitespace-nowrap">
                                <a href="{{ route('residents.edit',$r->id) }}"
                                   class="text-[12px] text-[var(--homi-blue)] hover:underline font-medium">
                                    Edit
                                </a>
                                <span class="mx-1 text-gray-300">|</span>
                                <form action="{{ route('residents.destroy',$r->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Yakin hapus data ini?')">
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
                            <td colspan="6"
                                class="px-6 py-6 text-center text-sm text-gray-400 border-t border-gray-200">
                                Tidak ada data.
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

    {{-- FORM TAMBAH WARGA BARU (hanya jika $users dikirim dari controller) --}}
    @isset($users)
        <div class="homi-card space-y-4">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">
                        Tambah Warga Baru
                    </h2>
                    <p class="text-[12px] text-gray-500">
                        Pilih user yang sudah terdaftar, lalu lengkapi data rumah & identitas keluarga.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('residents.store') }}" class="space-y-4 text-sm">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    {{-- PILIH USER --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-800">Pilih User</label>
                        <select name="user_id"
                                class="w-full rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] px-3 py-2">
                            <option value="">-- pilih user --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>
                                    {{ $u->full_name ?? $u->username }} — {{ $u->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NO RUMAH --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-800">No. Rumah</label>
                        <input type="text"
                               name="house_number"
                               value="{{ old('house_number') }}"
                               class="w-full rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] px-3 py-2">
                        @error('house_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ALAMAT --}}
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1 text-gray-800">Alamat</label>
                        <textarea name="address"
                                  rows="3"
                                  class="w-full rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] px-3 py-2">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NO IDENTITAS --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-800">No. Identitas (opsional)</label>
                        <input type="text"
                               name="id_number"
                               value="{{ old('id_number') }}"
                               class="w-full rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] px-3 py-2">
                        @error('id_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KEPALA KELUARGA --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-800">Kepala Keluarga</label>
                        <input type="text"
                               name="family_head"
                               value="{{ old('family_head') }}"
                               class="w-full rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] px-3 py-2">
                        @error('family_head')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KETERANGAN LAIN --}}
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1 text-gray-800">Keterangan Lain</label>
                        <textarea name="other_info"
                                  rows="3"
                                  class="w-full rounded-xl border-gray-300 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)] px-3 py-2">{{ old('other_info') }}</textarea>
                        @error('other_info')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <button
                        class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 text-sm font-semibold">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    @endisset
</div>
@endsection
