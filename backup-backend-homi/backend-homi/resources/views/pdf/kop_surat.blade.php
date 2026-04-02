<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    @page { margin: 1.5cm 2cm 2cm 2cm; }

    body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.3; color: #000; }

    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
    .kop-table td { vertical-align: middle; }
    .kop-logo { width: 80px; text-align: left; }
    .kop-logo img { width: 80px; height: auto; }

    .kop-text { text-align: center; padding-right: 80px; } /* padding to balance logo */
    .kop-text .l1 { font-weight: bold; font-size: 16pt; margin: 0; }
    .kop-text .l2 { font-weight: bold; font-size: 14pt; margin: 0; text-transform: uppercase; }
    .kop-text .l3 { font-weight: bold; font-size: 14pt; margin: 0; text-transform: uppercase; }
    .kop-text .l4 { font-size: 9pt; margin-top: 5px; font-style: normal; }

    .line-double { border-top: 3px solid #000; border-bottom: 1px solid #000; height: 3px; margin: 5px 0 20px 0; }

    /* Extra CSS from service/template */
    {{ $extraCss ?? '' }}
  </style>
</head>
<body>

@php
  $logoPath = public_path('images/logo.png');
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
      <div class="l1">PEMERINTAH KOTA BATAM</div>
      <div class="l2">KECAMATAN BATAM KOTA</div>
      <div class="l3">KELURAHAN BELIAN</div>
      <div class="l4">Jl. Sudirman No.14, Belian, Kec. Batam Kota, Kota Batam, Kepulauan Riau</div>
    </td>
  </tr>
</table>

<div class="line-double"></div>

{!! $body !!}

</body>
</html>
