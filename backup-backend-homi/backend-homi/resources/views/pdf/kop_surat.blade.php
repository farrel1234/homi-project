<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    @page { margin: 2.5cm 2.5cm 2.5cm 2.5cm; }

    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.5; }

    .kop-table { width: 100%; border-collapse: collapse; }
    .kop-table td { vertical-align: middle; }
    .kop-logo { width: 80px; }
    .kop-logo img { width: 70px; height: auto; display:block; }

    .kop-text { text-align: center; }
    .kop-text .l1 { font-weight: 700; font-size: 14px; text-transform: uppercase; }
    .kop-text .l2 { font-weight: 700; font-size: 13px; text-transform: uppercase; }
    .kop-text .l3 { font-size: 12px; text-transform: uppercase; }
    .kop-text .l4 { font-size: 11px; }

    .line { border-top: 2px solid #000; margin: 10px 0 14px; }

    /* Style tambahan dari template (kalau ada) */
    {{ $extraCss ?? '' }}
  </style>
</head>
<body>

@php
  $logoPath = public_path('images/logo.png'); // ganti kalau jpg
  $logoData = null;

  if (file_exists($logoPath)) {
      $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
      $mime = $ext === 'jpg' ? 'jpeg' : $ext;
      $logoData = 'data:image/'.$mime.';base64,'.base64_encode(file_get_contents($logoPath));
  }
@endphp

<table class="kop-table">
  <tr>
    <td class="kop-logo">
      @if($logoData)
        <img src="{{ $logoData }}" alt="Logo">
      @endif
    </td>
    <td class="kop-text">
      <div class="l1">{{ $kopLine1 ?? 'PENGELOLA PERUMAHAN HAWAI GARDEN' }}</div>
      <div class="l2">{{ $kopLine2 ?? 'LAYANAN WARGA' }}</div>
      <div class="l3">{{ $kopLine3 ?? 'KOTA BATAM' }}</div>
      <div class="l4">{{ $kopLine4 ?? 'Hawai Garden Batam Center' }}</div>
    </td>
    <td class="kop-logo"></td>
  </tr>
</table>

<div class="line"></div>

{{-- isi surat dari template_html --}}
{!! $body !!}

</body>
</html>
