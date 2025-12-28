<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(10);
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:units,code',
            'block' => 'nullable|string|max:20',
            'floor' => 'nullable|integer',
        ]);

        Unit::create($validated);
        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:units,code,' . $unit->id,
            'block' => 'nullable|string|max:20',
            'floor' => 'nullable|integer',
        ]);

        $unit->update($validated);
        return redirect()->route('units.index')->with('success', 'Data unit diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit dihapus.');
    }
}
