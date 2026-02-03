@extends('layouts.app')
@section('title','Edit Unit')

@section('content')
<div class="max-w-md mx-auto p-6">
  <h1 class="text-xl font-semibold mb-4">Edit Unit: {{ $unit->code }}</h1>

  @if($errors->any())
    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
      <ul class="list-disc ml-5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('units.update',$unit->id) }}" method="POST" class="space-y-3">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm mb-1">Kode <span class="text-red-600">*</span></label>
      <input type="text" name="code" value="{{ old('code', $unit->code) }}" class="w-full border rounded p-2" required>
      <p class="text-xs text-slate-500 mt-1">Kode harus unik.</p>
    </div>
    <div>
      <label class="block text-sm mb-1">Blok</label>
      <input type="text" name="block" value="{{ old('block', $unit->block) }}" class="w-full border rounded p-2">
    </div>
    <div>
      <label class="block text-sm mb-1">Lantai</label>
      <input type="number" name="floor" value="{{ old('floor', $unit->floor) }}" class="w-full border rounded p-2">
    </div>

    <div class="flex items-center gap-3 pt-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
      <a href="{{ route('units.index') }}" class="text-slate-600 hover:underline">Kembali</a>
    </div>
  </form>
</div>
@endsection
