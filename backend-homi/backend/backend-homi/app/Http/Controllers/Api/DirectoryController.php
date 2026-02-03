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
            ->select('id','name','email')
            ->where('role', 'resident')
            ->with('residentProfile:id,user_id,blok,no_rumah,alamat,is_public')
            ->whereHas('residentProfile', fn($p) => $p->where('is_public', true))
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhereHas('residentProfile', function($p) use ($q) {
                          $p->where('blok', 'like', "%{$q}%")
                            ->orWhere('no_rumah', 'like', "%{$q}%")
                            ->orWhere('alamat', 'like', "%{$q}%");
                      });
            })
            ->orderBy('name')
            ->paginate(20);

        $users->getCollection()->transform(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'blok' => $u->residentProfile?->blok,
            'no_rumah' => $u->residentProfile?->no_rumah,
            'blok_alamat' => ($u->residentProfile?->blok && $u->residentProfile?->no_rumah)
    ?           'Blok '.$u->residentProfile->blok.' No '.$u->residentProfile->no_rumah
                : ($u->residentProfile?->alamat ?? null),
        ]);


        return response()->json($users);
    }
}
