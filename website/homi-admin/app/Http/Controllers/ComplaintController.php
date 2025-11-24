<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $items = Complaint::with(['user', 'assigned'])
            ->when($q, fn($qz) =>
                $qz->where('title', 'like', "%$q%")
                   ->orWhere('description', 'like', "%$q%")
            )
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('complaints.index', compact('items', 'q'));
    }

    public function edit(Complaint $complaint)
    {
        $admins = User::where('role_id', 1)->get();

        return view('complaints.edit', [
            'item' => $complaint,
            'admins' => $admins,
        ]);
    }

    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status'      => ['required'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        if ($data['status'] === 'resolved') {
            $data['resolved_at'] = now();
        }

        $complaint->update($data);

        return redirect()
            ->route('complaints.index')
            ->with('ok', 'Status pengaduan berhasil diperbarui.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return redirect()
            ->route('complaints.index')
            ->with('ok', 'Pengaduan berhasil dihapus.');
    }
}
