<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        $users = User::query()
            ->select('id', 'name')
            ->where('role', 'resident')
            ->whereHas('residentProfile', function ($p) {
                $p->where('is_public', true);
            })
            ->with(['residentProfile:id,user_id,blok,no_rumah,alamat,is_public'])
            ->when($q !== '', function ($query) use ($q) {
                // ✅ grouping biar OR tidak kebablasan
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhereHas('residentProfile', function ($p) use ($q) {
                          $p->where('blok', 'like', "%{$q}%")
                            ->orWhere('no_rumah', 'like', "%{$q}%")
                            ->orWhere('alamat', 'like', "%{$q}%");
                      });
                });
            })
            ->orderBy('name')
            ->paginate(20);

        // ✅ transform item supaya "data" = List<DirectoryItem>
        $users->getCollection()->transform(function ($u) {
            $p = $u->residentProfile;

            return [
                'id' => $u->id,
                'name' => $u->name,
                'blok' => $p?->blok,
                'no_rumah' => $p?->no_rumah,
                'blok_alamat' => ($p?->blok && $p?->no_rumah)
                    ? 'Blok ' . $p->blok . ' No ' . $p->no_rumah
                    : ($p?->alamat ?? null),
            ];
        });

        // ✅ PENTING: return paginator langsung (agar cocok dengan DirectoryResponse)
        return response()->json($users);
    }
}
