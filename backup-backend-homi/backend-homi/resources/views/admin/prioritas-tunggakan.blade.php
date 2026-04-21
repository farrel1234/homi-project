@extends('layouts.app')

@section('content')
<div class="p-6">

    <h1 class="text-3xl font-bold mb-2 text-left">Prioritas Tunggakan</h1>

    <p class="text-gray-500 mb-6 text-left">
        Analisis Prioritas Penanganan Tunggakan Warga
    </p>

    @php
        $totalWarga   = count($data);
        $totalNominal = collect($data)->sum('tunggakan');
        $tinggi       = collect($data)->where('prioritas','Tinggi')->count();
        $sedang       = collect($data)->where('prioritas','Sedang')->count();
        $rendah       = collect($data)->where('prioritas','Rendah')->count();
    @endphp

    <!-- Statistik -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">

        <!-- Card 1 -->
        <div style="
            background:linear-gradient(135deg,#38bdf8,#2563eb);
            color:white;
            padding:20px;
            border-radius:18px;
            box-shadow:0 14px 30px rgba(37,99,235,0.20);
        ">
            <div style="font-size:14px;color:rgba(255,255,255,0.88);font-weight:500;">
                Total Warga Menunggak
            </div>

            <div style="font-size:32px;font-weight:800;margin-top:8px;">
                {{ $totalWarga }}
            </div>
        </div>

        <!-- Card 2 -->
        <div style="
            background:linear-gradient(135deg,#14b8a6,#059669);
            color:white;
            padding:20px;
            border-radius:18px;
            box-shadow:0 14px 30px rgba(5,150,105,0.20);
        ">
            <div style="font-size:14px;color:rgba(255,255,255,0.88);font-weight:500;">
                Jumlah Total Tunggakan
            </div>

            <div style="font-size:32px;font-weight:800;margin-top:8px;">
                Rp {{ number_format($totalNominal,0,',','.') }}
            </div>
        </div>

        <!-- Card 3 -->
        <div style="
            background:linear-gradient(135deg,#f87171,#dc2626);
            color:white;
            padding:20px;
            border-radius:18px;
            box-shadow:0 14px 30px rgba(220,38,38,0.20);
        ">
            <div style="
                font-size:14px;
                color:rgba(255,255,255,0.88);
                font-weight:500;
                margin-bottom:12px;
            ">
                Prioritas Tunggakan
            </div>

            <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;">
                <span>Tinggi</span>
                <span>{{ $tinggi }}</span>
            </div>

            <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;margin-top:8px;">
                <span>Sedang</span>
                <span>{{ $sedang }}</span>
            </div>

            <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;margin-top:8px;">
                <span>Rendah</span>
                <span>{{ $rendah }}</span>
            </div>
        </div>

    </div>

    <!-- Filter -->
<div style="display:flex;justify-content:flex-end;margin-bottom:18px;">

<form method="GET" action="{{ route('admin.prioritas-tunggakan') }}"
style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">

    <!-- Tahun -->
    <select name="tahun" style="
        padding:12px 18px;
        border:none;
        border-radius:999px;
        font-weight:600;
        font-size:14px;
        background:linear-gradient(135deg,#facc15,#eab308);
        color:white;
        cursor:pointer;
        outline:none;
    ">
        <option value="all" {{ request('tahun')=='all' ? 'selected':'' }} style="color:black;">Semua Tahun</option>
        <option value="2026" {{ request('tahun')=='2026' ? 'selected':'' }} style="color:black;">2026</option>
        <option value="2025" {{ request('tahun')=='2025' ? 'selected':'' }} style="color:black;">2025</option>
        <option value="2024" {{ request('tahun')=='2024' ? 'selected':'' }} style="color:black;">2024</option>
    </select>

    <!-- Bulan Awal -->
    <select name="bulan_awal" style="
        padding:12px 18px;
        border:none;
        border-radius:999px;
        font-weight:600;
        font-size:14px;
        background:linear-gradient(135deg,#a855f7,#7c3aed);
        color:white;
        cursor:pointer;
        outline:none;
    ">
        @for($i=1;$i<=12;$i++)
        <option value="{{ $i }}" {{ request('bulan_awal')==$i ? 'selected':'' }} style="color:black;">
            {{ date('F', mktime(0,0,0,$i,1)) }}
        </option>
        @endfor
    </select>

    <!-- Bulan Akhir -->
    <select name="bulan_akhir" style="
        padding:12px 18px;
        border:none;
        border-radius:999px;
        font-weight:600;
        font-size:14px;
        background:linear-gradient(135deg,#a855f7,#7c3aed);
        color:white;
        cursor:pointer;
        outline:none;
    ">
        @for($i=1;$i<=12;$i++)
        <option value="{{ $i }}" {{ request('bulan_akhir')==$i ? 'selected':'' }} style="color:black;">
            {{ date('F', mktime(0,0,0,$i,1)) }}
        </option>
        @endfor
    </select>

    <!-- Jenis Tagihan -->
    <select name="jenis" style="
        padding:12px 18px;
        border:none;
        border-radius:999px;
        font-weight:600;
        font-size:14px;
        background:linear-gradient(135deg,#ec4899,#db2777);
        color:white;
        cursor:pointer;
        outline:none;
        min-width:220px;
    ">

        <option value="all" {{ request('jenis')=='all' ? 'selected':'' }} style="color:black;">
            Semua Jenis
        </option>

        <option value="Iuran Sampah" {{ request('jenis')=='Iuran Sampah' ? 'selected':'' }} style="color:black;">
            Iuran Sampah
        </option>

        <option value="Iuran Keamanan" {{ request('jenis')=='Iuran Keamanan' ? 'selected':'' }} style="color:black;">
            Iuran Keamanan
        </option>

        <option value="Iuran Lingkungan" {{ request('jenis')=='Iuran Lingkungan' ? 'selected':'' }} style="color:black;">
            Iuran Lingkungan
        </option>

        <option value="Iuran Fasilitas Umum" {{ request('jenis')=='Iuran Fasilitas Umum' ? 'selected':'' }} style="color:black;">
            Iuran Fasilitas Umum
        </option>

    </select>

    <!-- Tombol Hitung -->
    <button type="submit" style="
        padding:12px 22px;
        border:none;
        border-radius:999px;
        font-weight:700;
        font-size:14px;
        background:linear-gradient(135deg,#06b6d4,#0891b2);
        color:white;
        cursor:pointer;
    ">
        Hitung Prioritas
            </button>

        </form>

    </div>

    <!-- Tabel -->
    <div style="
        background:white;
        padding:20px;
        border-radius:16px;
        box-shadow:0 10px 25px rgba(0,0,0,0.05);
    ">

        <table style="width:100%;border-collapse:collapse;text-align:center;">

            <tr style="background:#f8fafc;">
                <th style="padding:14px;">Rank</th>
                <th style="padding:14px;">Nama</th>
                <th style="padding:14px;">Total Tunggakan</th>
                <th style="padding:14px;">Periode</th>
                <th style="padding:14px;">Skor</th>
                <th style="padding:14px;">Prioritas</th>
            </tr>

            @forelse($data as $index => $item)
            <tr style="border-bottom:1px solid #f1f5f9;">

                <td style="padding:14px;">{{ $index + 1 }}</td>
                <td style="padding:14px;">{{ $item['nama'] }}</td>
                <td style="padding:14px;">Rp {{ number_format($item['tunggakan'],0,',','.') }}</td>
                <td style="padding:14px;">{{ $item['bulan'] }}</td>
                <td style="padding:14px;">{{ $item['skor'] }}</td>

                <td style="padding:14px;">

                    @if($item['prioritas'] == 'Tinggi')
                        <span style="padding:6px 14px;border-radius:999px;color:white;font-size:13px;font-weight:bold;background:linear-gradient(135deg,#ef4444,#dc2626);">
                            Tinggi
                        </span>
                    @elseif($item['prioritas'] == 'Sedang')
                        <span style="padding:6px 14px;border-radius:999px;color:white;font-size:13px;font-weight:bold;background:linear-gradient(135deg,#f59e0b,#d97706);">
                            Sedang
                        </span>
                    @else
                        <span style="padding:6px 14px;border-radius:999px;color:white;font-size:13px;font-weight:bold;background:linear-gradient(135deg,#22c55e,#16a34a);">
                            Rendah
                        </span>
                    @endif

                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:20px;color:#6b7280;">
                    Tidak ada data tunggakan.
                </td>
            </tr>
            @endforelse

        </table>

    </div>

</div>
@endsection