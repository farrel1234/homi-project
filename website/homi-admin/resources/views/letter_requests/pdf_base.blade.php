<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat - {{ $item->type->name ?? 'HOMI' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .kop {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }
        .kop .title {
            font-weight: bold;
            font-size: 14px;
        }
        .content {
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="kop">
        <div class="title">PERUMAHAN HAWAI GARDEN</div>
        <div>Sistem Informasi Layanan Warga HOMI</div>
    </div>

    <div class="content">
        {!! $html !!}
    </div>
</body>
</html>
