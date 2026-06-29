<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\AppNotification;
use App\Services\FirebaseService;
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
        $admins = User::query()
            ->where(function ($q) {
                $q->where('role_id', 1)
                    ->orWhereIn('role', ['admin', 'superadmin']);
            })
            ->orderByRaw('COALESCE(full_name, name) asc')
            ->get();

        return view('complaints.edit', [
            'item'   => $complaint,
            'admins' => $admins,
        ]);
    }

    public function update(Request $request, Complaint $complaint, FirebaseService $firebaseService)
    {
        $data = $request->validate([
            'status'      => ['required', 'in:baru,diproses,selesai'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'rt_name'     => ['nullable', 'string', 'max:255'],
            'rt_number'   => ['nullable', 'string', 'max:50'],
            'rw_number'   => ['nullable', 'string', 'max:50'],
        ], [
            'status.in' => 'The selected status is invalid.',
        ]);

        if (($data['status'] ?? null) === 'selesai') {
            $data['resolved_at'] = now();
        } else {
            $data['resolved_at'] = null;
        }

        $oldStatus = $complaint->status;
        $complaint->update($data);

        // Send Notification if status changed
        if ($oldStatus !== $complaint->status) {
            $statusLabels = [
                'baru' => 'Baru',
                'diproses' => 'Diproses',
                'selesai' => 'Selesai'
            ];
            $newLabel = $statusLabels[$complaint->status] ?? $complaint->status;
            
            $title = "Update Pengaduan";
            $msg = "Status pengaduan Anda '" . ($complaint->perihal ?? 'Layanan') . "' kini: " . $newLabel;
            
            AppNotification::create([
                'user_id' => $complaint->user_id,
                'sent_by' => auth()->id(),
                'title'   => $title,
                'message' => $msg,
                'type'    => 'complaint_status_updated',
                'data'    => ['id' => $complaint->id, 'route' => 'DetailPengaduan']
            ]);

            if ($complaint->user && $complaint->user->fcm_token) {
                $firebaseService->sendNotification(
                    $complaint->user->fcm_token,
                    $title,
                    $msg,
                    ['route' => 'DetailPengaduan', 'id' => (string)$complaint->id]
                );
            }
        }

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
