<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QrCodeController extends Controller
{
    /**
     * Upload QR baru.
     * Default: jadi aktif (kalau is_active tidak dikirim).
     */
    public function store(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_active' => 'nullable|boolean',
            'notes'     => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('payment_qr_codes', 'public');
        $isActive = filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN);

        $qr = DB::transaction(function () use ($request, $path, $isActive) {
            if ($isActive) {
                PaymentQrCode::query()->update(['is_active' => false]);
            }

            return PaymentQrCode::create([
                'image_path' => $path,
                'is_active'  => $isActive,
                'notes'      => $request->input('notes'),
                'updated_by' => optional($request->user())->id,
            ]);
        });

        // bikin URL berdasarkan host request (bukan APP_URL)
        $imageUrl = $this->makePublicStorageUrl($request, $qr->image_path);

        return response()->json([
            'message' => 'QR code saved',
            'data' => [
                'id'         => $qr->id,
                'is_active'  => (bool) $qr->is_active,
                'notes'      => $qr->notes,
                'image_url'  => $imageUrl,
                'updated_at' => optional($qr->updated_at)->toISOString(),
            ],
        ], 201);
    }

    /**
     * Ambil QR aktif untuk client.
     */
    public function active(Request $request)
    {
        $qr = PaymentQrCode::query()
            ->active()
            ->latest('id')
            ->first();

        if (!$qr) {
            return response()->json(['message' => 'No active QR code'], 404);
        }

        // bikin URL berdasarkan host request (bukan APP_URL)
        $imageUrl = $this->makePublicStorageUrl($request, $qr->image_path);

        return response()->json([
            'data' => [
                'id'         => $qr->id,
                'is_active'  => (bool) $qr->is_active,
                'notes'      => $qr->notes,
                'image_url'  => $imageUrl,
                'updated_at' => optional($qr->updated_at)->toISOString(),
            ]
        ]);
    }

    /**
     * Helper: build full URL untuk file yang ada di storage/app/public/{path}
     * hasil: http(s)://HOST/storage/{path}
     */
    private function makePublicStorageUrl(Request $request, ?string $path): ?string
    {
        if (!$path) return null;

        // kalau path sudah full http(s), balikin aja
        if (preg_match('/^https?:\/\//i', $path)) return $path;

        // normalisasi path supaya aman
        $p = str_replace('\\', '/', (string) $path);
        $p = ltrim($p, '/');
        $p = preg_replace('#^storage/#', '', $p);
        $p = preg_replace('#^public/#', '', $p);

        // public storage link standard Laravel:
        // public/storage -> storage/app/public
        $relative = '/storage/' . ltrim($p, '/');

        return $request->getSchemeAndHttpHost() . $relative;
    }
}
