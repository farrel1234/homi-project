<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // GET /api/announcements
    public function index(Request $request)
    {
        $query = Announcement::whereNotNull('published_at');

        // Filter: Hanya yang publik (jika kolom ada)
        if (\Illuminate\Support\Facades\Schema::hasColumn('announcements', 'is_public')) {
            $query->where('is_public', true);
        }

        // Filter: Penjadwalan (jika kolom ada)
        $now = now();
        if (\Illuminate\Support\Facades\Schema::hasColumn('announcements', 'start_at')) {
            $query->where(function($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            });
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('announcements', 'end_at')) {
            $query->where(function($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
        }

        // Hanya filter tenant_id jika kolomnya ada DAN kita tidak sedang di database tenant (Dedicated)
        // Jika app('currentTenant') ada, berarti koneksi DB sudah terisolasi ke perumahan tsb.
        if (\Illuminate\Support\Facades\Schema::hasColumn('announcements', 'tenant_id') && !app()->bound('currentTenant')) {
            $query->where('tenant_id', $request->user()->tenant_id);
        }

        $announcements = $query->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $announcements,
        ]);
    }

    // GET /api/announcements/{id}
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $announcement,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // pastikan hanya admin yang boleh
        if ($user->role !== 'admin') {
            return response()->json([
                'status'  => false,
                'message' => 'Hanya admin yang dapat membuat pengumuman.',
            ], 403);
        }

        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'is_pinned' => 'nullable|boolean',
            'image'     => 'nullable|image|max:2048', // max ~2MB
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            // file akan disimpan di storage/app/public/announcements
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        $hasTenantId = \Illuminate\Support\Facades\Schema::hasColumn('announcements', 'tenant_id');

        $announcement = Announcement::create(array_filter([
            'title'        => $data['title'],
            'tenant_id'    => $hasTenantId ? $user->tenant_id : null,
            'content'      => $data['content'],
            'is_pinned'    => $request->boolean('is_pinned'),
            'published_at' => now(),
            'created_by'   => $user->id,
            'image_path'   => $imagePath,
        ], fn($val, $key) => ($key === 'tenant_id' && !$hasTenantId) ? false : true));

        // 🔥 Broadcast Kirim Push Notification FCM ke semua user di tenant yang sama
        $firebase = new \App\Services\FirebaseService();
        $users = \App\Models\User::where('tenant_id', $user->tenant_id)
            ->whereNotNull('fcm_token')
            ->get();
        
        $bodyText = strlen($announcement->content) > 60 
            ? substr($announcement->content, 0, 60) . '...' 
            : $announcement->content;

        foreach ($users as $u) {
            $firebase->sendNotification(
                $u->fcm_token,
                "Pengumuman Baru: " . $announcement->title,
                $bodyText
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Pengumuman berhasil dibuat dan notifikasi dikirim.',
            'data'    => $announcement,
        ], 201);
    }

}
