<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FeeQrController extends Controller
{
    public function index()
    {
        $active = PaymentQrCode::query()
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $items = PaymentQrCode::query()
            ->latest('id')
            ->paginate(10);

        return view('admin.fees.qr.index', compact('active', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $path = $request->file('qr_image')->store('payment_qr_codes', 'public');

        DB::transaction(function () use ($request, $path) {
            PaymentQrCode::query()->update(['is_active' => false]);

            PaymentQrCode::create([
                'image_path' => $path,
                'is_active'  => true,
                'notes'      => 'Uploaded via admin',
                'updated_by' => optional($request->user())->id,
            ]);
        });

        return back()->with('success', 'QR baru berhasil diupload dan diaktifkan.');
    }

    public function activate($id)
    {
        DB::transaction(function () use ($id) {
            PaymentQrCode::query()->update(['is_active' => false]);
            PaymentQrCode::query()->whereKey($id)->update(['is_active' => true]);
        });

        return back()->with('success', 'QR berhasil dijadikan aktif.');
    }

    /**
     * ✅ Hapus QR (hanya nonaktif).
     * File gambar di storage juga ikut dihapus.
     */
    public function destroy($id)
    {
        $qr = PaymentQrCode::query()->findOrFail($id);

        if ((bool) $qr->is_active) {
            return back()->with('error', 'QR yang sedang aktif tidak boleh dihapus. Aktifkan QR lain terlebih dahulu.');
        }

        DB::transaction(function () use ($qr) {
            // hapus file storage jika ada
            $path = $qr->image_path ?? null;
            if ($path) {
                $p = str_replace('\\', '/', (string) $path);
                $p = ltrim($p, '/');
                $p = preg_replace('#^storage/#', '', $p);
                $p = preg_replace('#^public/#', '', $p);

                // delete di disk public (storage/app/public)
                Storage::disk('public')->delete($p);
            }

            $qr->delete();
        });

        return back()->with('success', 'QR nonaktif berhasil dihapus.');
    }
}
