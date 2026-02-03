<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat - {{ $item->type->name ?? 'HOMI' }}</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 3cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }

        .kop {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .kop .title {
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }

        .kop .subtitle {
            font-size: 12px;
            text-transform: uppercase;
        }

        .kop .address {
            font-size: 10px;
            margin-top: 4px;
        }

        .content {
            margin-top: 18px;
            font-size: 11px;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT RESMI --}}
    <div class="kop">
    <div class="title">PENGELOLA PERUMAHAN HAWAI GARDEN</div>
    <div class="subtitle">KOTA BATAM</div>
    <div class="address">
        Jl. Raya Abulyatama, Hawai Garden, Blok B 3a,<br>
        Kecamatan Batam Kota, Kelurahan Belian, Kota Batam<br>
        Telp: 0811-778-8181
    </div>
</div>

<div class="content">
    {!! $html !!}
</div>


</body>
</html>
