@extends('layouts.app')
@section('title','Data Unit')

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Data Unit</h1>
    <a href="{{ route('units.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">+ Tambah Unit</a>
  </div>

  @if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
      <ul class="list-disc ml-5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <table class="w-full border-collapse border text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="border p-2 w-14">#</th>
        <th class="border p-2">Kode</th>
        <th class="border p-2">Blok</th>
        <th class="border p-2">Lantai</th>
        <th class="border p-2 w-40">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($units as $u)
        <tr>
          <td class="border p-2">{{ ($units->currentPage()-1)*$units->perPage() + $loop->iteration }}</td>
          <td class="border p-2 font-medium">{{ $u->code }}</td>
          <td class="border p-2">{{ $u->block ?? '-' }}</td>
          <td class="border p-2">{{ $u->floor ?? '-' }}</td>
          <td class="border p-2">
            <a href="{{ route('units.edit',$u->id) }}" class="text-blue-600">Edit</a>
            <span class="text-slate-400 px-1">|</span>
            <form action="{{ route('units.destroy',$u->id) }}" method="POST" class="inline"
                  onsubmit="return confirm('Hapus unit {{ $u->code }} ?')">
              @csrf @method('DELETE')
              <button class="text-red-600">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center border p-3">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-4">{{ $units->links() }}</div>

  <div class="mt-6">
    <a href="{{ route('admin.dashboard') }}" class="text-sky-700 hover:underline">← Kembali ke Dashboard</a>
  </div>
</div>
@endsection
