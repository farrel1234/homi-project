<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    // helper untuk format tanggal: 1 Oktober 2025
    private function formatTanggalIndo(string $tanggal): string
    {
        $months = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $date = Carbon::parse($tanggal);
        $day  = $date->day;
        $monthName = $months[$date->month] ?? $date->month;
        $year = $date->year;

        return "{$day} {$monthName} {$year}";
    }

    // GET /api/complaints  -> riwayat pengaduan user login
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $complaints = Complaint::where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function (Complaint $c) {
                return [
                    'id'          => $c->id,
                    'nama_pelapor'=> $c->nama_pelapor,
                    'tanggal'     => $this->formatTanggalIndo($c->tanggal_pengaduan),
                    'tanggal_iso' => $c->tanggal_pengaduan,
                    'tempat'      => $c->tempat_kejadian,
                    'perihal'     => $c->perihal,
                    'status'      => $c->status,
                    'foto_url'    => $c->foto_url,
                    'created_at'  => $c->created_at,
                ];
            });

        return response()->json([
            'message' => 'Riwayat pengaduan berhasil diambil',
            'data'    => $complaints,
        ]);
    }

    // GET /api/complaints/{complaint}  -> detail 1 pengaduan
    public function show(Complaint $complaint): JsonResponse
    {
        // pastikan hanya pemilik yang boleh lihat
        if (request()->user()->id !== $complaint->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = [
            'id'          => $complaint->id,
            'nama_pelapor'=> $complaint->nama_pelapor,
            'tanggal'     => $this->formatTanggalIndo($complaint->tanggal_pengaduan),
            'tanggal_iso' => $complaint->tanggal_pengaduan,
            'tempat'      => $complaint->tempat_kejadian,
            'perihal'     => $complaint->perihal,
            'status'      => $complaint->status,
            'foto_url'    => $complaint->foto_url,
            'created_at'  => $complaint->created_at,
        ];

        return response()->json([
            'message' => 'Detail pengaduan berhasil diambil',
            'data'    => $data,
        ]);
    }

    // POST /api/complaints
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_pelapor'      => 'required|string|max:255',
            'tanggal_pengaduan' => 'required|string',
            'tempat_kejadian'   => 'required|string|max:255',
            'perihal'           => 'required|string|max:255',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ===== KONVERSI TANGGAL KE Y-m-d =====
        $rawTanggal = trim($request->input('tanggal_pengaduan'));

        $allowedFormats = [
            'd-m-Y',
            'j-n-Y',
            'Y-m-d',
            'Y-n-j',
        ];

        $tanggal = null;

        foreach ($allowedFormats as $format) {
            try {
                $dt = Carbon::createFromFormat($format, $rawTanggal);
                $tanggal = $dt->format('Y-m-d');
                break;
            } catch (\Exception $e) {
                // lanjut format berikutnya
            }
        }

        if (!$tanggal) {
            return response()->json([
                'message' => 'Format tanggal tidak valid. Gunakan dd-mm-yyyy atau yyyy-mm-dd.',
            ], 422);
        }

        // ===== UPLOAD FOTO (opsional) =====
        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('complaints', 'public');
        }

        $complaint = Complaint::create([
            'user_id'           => $request->user()->id,
            'nama_pelapor'      => $validated['nama_pelapor'],
            'tanggal_pengaduan' => $tanggal,
            'tempat_kejadian'   => $validated['tempat_kejadian'],
            'perihal'           => $validated['perihal'],
            'foto_path'         => $fotoPath,
        ]);

        return response()->json([
            'message'  => 'Pengaduan berhasil dikirim',
            'data'     => $complaint,
            'foto_url' => $fotoPath ? asset('storage/' . $fotoPath) : null,
        ], 201);
    }
}
