<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseService;
use App\Services\HomiNotificationService;

class AppNotificationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $items = AppNotification::query()
            ->with(['user', 'sender'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                       ->orWhere('message', 'like', "%{$q}%");
                })->orWhereHas('user', function ($u) use ($q) {
                    $u->where('full_name', 'like', "%{$q}%")
                      ->orWhere('name', 'like', "%{$q}%")
                      ->orWhere('username', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->paginate(20);

        return view('admin.notifications.index', compact('items', 'q'));
    }

    public function create()
    {
        $users = User::query()->orderBy('id')->limit(300)->get();
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request, FirebaseService $firebaseService)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title'   => ['required', 'string', 'max:200'],
            'message' => ['required', 'string'],
            'type'    => ['nullable', 'string', 'max:50'],
            'route'   => ['nullable', 'string', 'max:100'],
            'invoice_id' => ['nullable', 'integer'],
            'period' => ['nullable', 'string', 'max:20'],
        ]);

        $payload = [];
        if (!empty($data['route'])) $payload['route'] = $data['route'];
        if (!empty($data['invoice_id'])) $payload['invoice_id'] = (int)$data['invoice_id'];
        if (!empty($data['period'])) $payload['period'] = $data['period'];

        $notif = AppNotification::create([
            'user_id' => (int)$data['user_id'],
            'sent_by' => Auth::id(),
            'title'   => $data['title'],
            'message' => $data['message'],
            'type'    => $data['type'] ?: 'general',
            'data'    => empty($payload) ? null : $payload,
        ]);

        // Send FCM if token exists
        $user = User::find($data['user_id']);
        if ($user && $user->fcm_token) {
            $firebaseService->sendNotification(
                $user->fcm_token,
                $data['title'],
                $data['message'],
                $payload
            );
        }

        return redirect()->route('admin.notifications.index')->with('ok', 'Notifikasi berhasil dikirim.');
    }

    public function sendRiskWarning(Request $request, $userId, HomiNotificationService $notifier)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'period' => ['nullable', 'string', 'max:20'],
            'invoice_id' => ['nullable', 'integer'],
            'score' => ['nullable', 'numeric'],
        ]);

        $period = $validated['period'] ?? null;
        $score  = isset($validated['score']) ? round((float)$validated['score'] * 100) : null;

        $title = '🔔 Pengingat Iuran (AI Risk Warning)';
        $msg = 'Halo ' . ($user->full_name ?? $user->name ?? 'Warga') .
            ', kami mengingatkan iuran Anda agar tidak terlambat. ' .
            ($period ? "Periode: {$period}. " : '') .
            ($score !== null ? "\n\nSistem AI kami mendeteksi risiko keterlambatan pada pembayaran ini. " : '') .
            'Mohon segera lakukan pelunasan untuk kenyamanan bersama.';

        $payload = ['route' => 'TagihanIuran'];
        if (!empty($validated['invoice_id'])) $payload['invoice_id'] = (int)$validated['invoice_id'];
        if ($period) $payload['period'] = $period;
        if ($score) $payload['ai_score'] = $validated['score'];

        // Menggunakan service multi-channel (In-App, FCM, Email, WA)
        $notifier->notify($user, $title, $msg, 'risk_warning', $payload);

        return back()->with('ok', 'Notifikasi pengingat multi-channel berhasil dikirim.');
    }
}
