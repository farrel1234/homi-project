<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = (int) $request->query('per_page', 20);
        $unreadOnly = filter_var($request->query('unread'), FILTER_VALIDATE_BOOLEAN);

        $q = DB::table('app_notifications')
            ->where('user_id', $user->id)
            ->when($unreadOnly, fn($qq) => $qq->whereNull('read_at'))
            ->orderByDesc('created_at');

        $items = $q->paginate($perPage);

        // decode data json kalau ada
        $items->getCollection()->transform(function ($row) {
            $row->data = $row->data ? json_decode($row->data, true) : null;
            $row->is_read = !is_null($row->read_at);
            return $row;
        });

        return response()->json($items);
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $count = DB::table('app_notifications')
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function markRead(Request $request, $id)
    {
        $user = $request->user();

        $updated = DB::table('app_notifications')
            ->where('id', (int)$id)
            ->where('user_id', $user->id)
            ->update(['read_at' => now(), 'updated_at' => now()]);

        if (!$updated) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        return response()->json(['ok' => true]);
    }

    public function readAll(Request $request)
    {
        $user = $request->user();

        $updated = DB::table('app_notifications')
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now(), 'updated_at' => now()]);

        return response()->json(['ok' => true, 'marked' => $updated]);
    }
}
