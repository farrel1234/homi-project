<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $items = Complaint::query()
            ->with(['user', 'assigned'])
            ->when($status !== '', function ($query) use ($status) {
                // status enum DB: baru/diproses/selesai
                $query->where('status', $status);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('perihal', 'like', "%{$q}%")
                       ->orWhere('nama_pelapor', 'like', "%{$q}%")
                       ->orWhere('tempat_kejadian', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('complaints.index', compact('items', 'q', 'status'));
    }

    public function edit(Complaint $complaint)
    {
        // sesuaikan kalau admin kamu bukan role_id=1
        $admins = User::where('role_id', 1)->orderBy('full_name')->get();

        return view('complaints.edit', [
            'item'   => $complaint,
            'admins' => $admins,
        ]);
    }

    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status'      => ['required', 'in:baru,diproses,selesai'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ], [
            'status.in' => 'The selected status is invalid.',
        ]);

        if (($data['status'] ?? null) === 'selesai') {
            $data['resolved_at'] = now();
        } else {
            $data['resolved_at'] = null;
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
