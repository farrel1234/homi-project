<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $query = Resident::with('user');

        if ($q) {
            $query->where(function ($r) use ($q) {
                $r->where('house_number', 'like', "%$q%")
                  ->orWhere('address', 'like', "%$q%")
                  ->orWhere('family_head', 'like', "%$q%")
                  ->orWhereHas('user', function ($uq) use ($q) {
                      $uq->where('full_name', 'like', "%$q%")
                         ->orWhere('username', 'like', "%$q%")
                         ->orWhere('email', 'like', "%$q%");
                  });
            });
        }

        $items = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('residents.index', compact('items', 'q'));
    }

    public function create()
    {
        // Ambil user role warga (role_id = 2) yang belum punya resident
        $users = User::where('role_id', 2)
            ->whereNotIn('id', function ($q) {
                $q->select('user_id')->from('residents');
            })
            ->orderBy('full_name')
            ->get();

        return view('residents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'      => ['required', 'exists:users,id', 'unique:residents,user_id'],
            'house_number' => ['nullable', 'string', 'max:50'],
            'address'      => ['nullable', 'string'],
            'id_number'    => ['nullable', 'string', 'max:100'],
            'family_head'  => ['nullable', 'string', 'max:255'],
            'other_info'   => ['nullable', 'string'],
        ]);

        Resident::create($data);

        return redirect()
            ->route('residents.index')
            ->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function edit(Resident $resident)
    {
        // User warga lain + user milik resident ini
        $users = User::where('role_id', 2)
            ->where(function ($q) use ($resident) {
                $q->whereNotIn('id', function ($sub) use ($resident) {
                    $sub->select('user_id')->from('residents')->where('id', '!=', $resident->id);
                })->orWhere('id', $resident->user_id);
            })
            ->orderBy('full_name')
            ->get();

        return view('residents.edit', compact('resident', 'users'));
    }

    public function update(Request $request, Resident $resident)
    {
        $data = $request->validate([
            'user_id'      => [
                'required',
                'exists:users,id',
                Rule::unique('residents', 'user_id')->ignore($resident->id),
            ],
            'house_number' => ['nullable', 'string', 'max:50'],
            'address'      => ['nullable', 'string'],
            'id_number'    => ['nullable', 'string', 'max:100'],
            'family_head'  => ['nullable', 'string', 'max:255'],
            'other_info'   => ['nullable', 'string'],
        ]);

        $resident->update($data);

        return redirect()
            ->route('residents.index')
            ->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(Resident $resident)
    {
        $resident->delete();

        return redirect()
            ->route('residents.index')
            ->with('success', 'Data warga berhasil dihapus.');
    }
}
