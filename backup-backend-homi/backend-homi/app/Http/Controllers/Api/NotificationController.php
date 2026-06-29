<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = (int) $request->user()->id;

        // Ambil daftar notification_id yang sudah dibaca user ini
        $readIds = DB::table('app_notification_reads')
            ->where('user_id', $userId)
            ->pluck('notification_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $readSet = array_flip($readIds);

        $items = AppNotification::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(20);

        $data = $items->getCollection()->map(function ($n) use ($readSet) {
            // data bisa string JSON atau sudah array (kalau ada casts)
            $metaRaw = $n->data ?? null;

            if (is_array($metaRaw)) {
                $meta = $metaRaw;
            } elseif (is_string($metaRaw) && $metaRaw !== '') {
                $decoded = json_decode($metaRaw, true);
                $meta = is_array($decoded) ? $decoded : null;
            } else {
                $meta = null;
            }

            return [
                'id'         => (int) $n->id,
                'type'       => (string) $n->type,
                'title'      => (string) $n->title,
                'body'       => (string) ($n->message ?? ''), // <- kolom kamu: message
                'meta'       => $meta,                        // <- kolom kamu: data (decoded)
                'created_at' => optional($n->created_at)->toIso8601String(),
                'is_read'    => isset($readSet[(int) $n->id]),
            ];
        })->values();

        return response()->json([
            'data'         => $data,
            'current_page' => $items->currentPage(),
            'last_page'    => $items->lastPage(),
            'per_page'     => $items->perPage(),
            'total'        => $items->total(),
        ]);
    }

    public function unreadCount(Request $request)
    {
        $userId = (int) $request->user()->id;

        $readIds = DB::table('app_notification_reads')
            ->where('user_id', $userId)
            ->pluck('notification_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $count = AppNotification::query()
            ->where('user_id', $userId)
            ->when(!empty($readIds), fn ($q) => $q->whereNotIn('id', $readIds))
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function markRead(Request $request, int $id)
    {
        $userId = (int) $request->user()->id;
        $id     = (int) $id;

        // pastikan notif milik user ini
        $exists = AppNotification::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $now = now();

        DB::table('app_notification_reads')->updateOrInsert(
            ['user_id' => $userId, 'notification_id' => $id],
            ['read_at' => $now, 'created_at' => $now, 'updated_at' => $now]
        );

        return response()->json(['ok' => true]);
    }

    public function readAll(Request $request)
    {
        $userId = (int) $request->user()->id;

        $notifIds = AppNotification::query()
            ->where('user_id', $userId)
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (empty($notifIds)) {
            return response()->json(['ok' => true]);
        }

        $now = now();

        $rows = array_map(fn ($nid) => [
            'user_id'         => $userId,
            'notification_id' => (int) $nid,
            'read_at'         => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ], $notifIds);

        DB::table('app_notification_reads')->upsert(
            $rows,
            ['user_id', 'notification_id'],
            ['read_at', 'updated_at']
        );

        return response()->json(['ok' => true]);
    }
}
