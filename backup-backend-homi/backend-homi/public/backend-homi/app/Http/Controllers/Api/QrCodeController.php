<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentQrCode;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_active' => 'nullable|boolean',
            'notes'     => 'nullable|string|max:255', // ini pengganti "name"
        ]);

        $path = $request->file('image')->store('payment_qr_codes', 'public');
        $isActive = filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN);

        if ($isActive) {
            PaymentQrCode::query()->update(['is_active' => false]);
        }

        $qr = PaymentQrCode::create([
            'image_path' => $path,
            'is_active'  => $isActive,
            'notes'      => $request->input('notes'),
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'QR code saved',
            'data' => [
                'id'        => $qr->id,
                'is_active' => (bool) $qr->is_active,
                'notes'     => $qr->notes,
                'image_url' => asset('storage/' . $qr->image_path),
            ],
        ], 201);
    }

    public function active()
    {
        $qr = PaymentQrCode::query()
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$qr) {
            return response()->json(['message' => 'No active QR code'], 404);
        }

        return response()->json([
            'data' => [
                'id'        => $qr->id,
                'is_active' => (bool) $qr->is_active,
                'notes'     => $qr->notes,
                'image_url' => asset('storage/' . $qr->image_path),
            ]
        ]);
    }
}
