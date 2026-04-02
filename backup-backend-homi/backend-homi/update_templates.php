<?php

use App\Models\LetterType;

$letters = [
    1 => [
        'name' => 'Surat Keterangan Domisili',
        'template_html' => '<style>
  .title{ text-align:center; font-weight:bold; font-size:16px; text-transform:uppercase; margin-top:20px;}
  .nomor{ text-align:center; margin-top:5px; margin-bottom:20px; }
  table.data{ width:100%; border-collapse:collapse; margin:15px 0; }
  table.data td{ padding:5px 0; vertical-align:top; }
  .sign{ width:100%; margin-top:30px; }
  .sign td{ vertical-align:top; }
</style>

<div class="title">SURAT KETERANGAN DOMISILI</div>
<div class="nomor">Nomor: {{nomor_surat}}</div>

<p>Yang bertanda tangan di bawah ini Ketua Rukun Tetangga (RT) menerangkan bahwa:</p>

<table class="data">
  <tr><td style="width:180px;">Nama Lengkap</td><td>: {{nama}}</td></tr>
  <tr><td>NIK</td><td>: {{nik}}</td></tr>
  <tr><td>Tempat/Tgl Lahir</td><td>: {{tempat_lahir}} / {{tanggal_lahir}}</td></tr>
  <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>
  <tr><td>Alamat (KTP)</td><td>: {{alamat}}</td></tr>
  <tr><td>Domisili Saat Ini</td><td>: Perumahan Hawai Garden Blok {{blok}} No {{no_rumah}}</td></tr>
</table>

<p>Benar nama tersebut di atas adalah warga yang berdomisili di lingkungan kami.</p>

<p>Surat keterangan domisili ini diberikan untuk memenuhi persyaratan:</p>
<table class="data">
  <tr><td style="width:180px;">Tujuan / Instansi</td><td>: {{tujuan_instansi}}</td></tr>
  <tr><td>Keperluan</td><td>: {{keperluan}}</td></tr>
</table>

<p>Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>

<table class="sign">
  <tr>
    <td style="width:60%;"></td>
    <td>Batam, {{tanggal_surat}}<br>Ketua RT {{rt}} / RW {{rw}}<br><br><br><br><b><u>{{nama_rt}}</u></b></td>
  </tr>
</table>',
        'required_json' => ['nomor_surat', 'tanggal_surat', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'rt', 'rw', 'nama_rt', 'tujuan_instansi', 'keperluan']
    ],
    2 => [
        'name' => 'Surat Pengantar',
        'template_html' => '<style>
  .title{ text-align:center; font-weight:bold; font-size:16px; text-transform:uppercase; margin-top:20px;}
  .nomor{ text-align:center; margin-top:5px; margin-bottom:20px; }
  table.data{ width:100%; border-collapse:collapse; margin:15px 0; }
  table.data td{ padding:5px 0; vertical-align:top; }
  .sign{ width:100%; margin-top:30px; }
  .sign td{ vertical-align:top; }
</style>

<div class="title">SURAT PENGANTAR</div>
<div class="nomor">Nomor: {{nomor_surat}}</div>

<p>Yang bertanda tangan di bawah ini Ketua Rukun Tetangga (RT) menerangkan bahwa:</p>

<table class="data">
  <tr><td style="width:180px;">Nama Lengkap</td><td>: {{nama}}</td></tr>
  <tr><td>NIK</td><td>: {{nik}}</td></tr>
  <tr><td>Tempat/Tgl Lahir</td><td>: {{tempat_lahir}} / {{tanggal_lahir}}</td></tr>
  <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>
  <tr><td>Alamat</td><td>: {{alamat}}</td></tr>
</table>

<p>Surat pengantar ini diberikan untuk keperluan:</p>
<table class="data">
  <tr><td style="width:180px;">Tujuan / Instansi</td><td>: {{tujuan_instansi}}</td></tr>
  <tr><td>Keperluan</td><td>: {{keperluan}}</td></tr>
</table>

<p>Demikian surat pengantar ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

<table class="sign">
  <tr>
    <td style="width:60%;"></td>
    <td>Batam, {{tanggal_surat}}<br>Ketua RT {{rt}} / RW {{rw}}<br><br><br><br><b><u>{{nama_rt}}</u></b></td>
  </tr>
</table>',
        'required_json' => ['nomor_surat', 'tanggal_surat', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'rt', 'rw', 'nama_rt', 'tujuan_instansi', 'keperluan']
    ],
    4 => [
        'name' => 'Surat Keterangan Usaha',
        'template_html' => '<style>
  .title{ text-align:center; font-weight:bold; font-size:16px; text-transform:uppercase; margin-top:20px;}
  .nomor{ text-align:center; margin-top:5px; margin-bottom:20px; }
  table.data{ width:100%; border-collapse:collapse; margin:15px 0; }
  table.data td{ padding:5px 0; vertical-align:top; }
  .sign{ width:100%; margin-top:30px; }
  .sign td{ vertical-align:top; }
</style>

<div class="title">SURAT KETERANGAN USAHA</div>
<div class="nomor">Nomor: {{nomor_surat}}</div>

<p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>

<table class="data">
  <tr><td style="width:180px;">Nama Lengkap</td><td>: {{nama}}</td></tr>
  <tr><td>NIK</td><td>: {{nik}}</td></tr>
  <tr><td>Tempat/Tgl Lahir</td><td>: {{tempat_lahir}} / {{tanggal_lahir}}</td></tr>
  <tr><td>Alamat</td><td>: {{alamat}}</td></tr>
</table>

<p>Benar yang bersangkutan memiliki usaha dengan identitas sebagai berikut:</p>

<table class="data">
  <tr><td style="width:180px;">Nama Usaha</td><td>: {{nama_usaha}}</td></tr>
  <tr><td>Jenis/Bidang Usaha</td><td>: {{jenis_usaha}}</td></tr>
  <tr><td>Alamat Usaha</td><td>: {{alamat_usaha}}</td></tr>
  <tr><td>Lama Usaha</td><td>: {{lama_usaha}}</td></tr>
</table>

<p>Surat keterangan usaha ini dibuat untuk keperluan:</p>
<table class="data">
  <tr><td style="width:180px;">Keperluan</td><td>: {{keperluan}}</td></tr>
</table>

<p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

<table class="sign">
  <tr>
    <td style="width:60%;"></td>
    <td>Batam, {{tanggal_surat}}<br>Ketua RT {{rt}} / RW {{rw}}<br><br><br><br><b><u>{{nama_rt}}</u></b></td>
  </tr>
</table>',
        'required_json' => ['nomor_surat', 'tanggal_surat', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'rt', 'rw', 'nama_rt', 'nama_usaha', 'jenis_usaha', 'alamat_usaha', 'keperluan']
    ],
    5 => [
        'name' => 'Surat Keterangan Belum Menikah',
        'template_html' => '<style>
  .title{ text-align:center; font-weight:bold; font-size:16px; text-transform:uppercase; margin-top:20px;}
  .nomor{ text-align:center; margin-top:5px; margin-bottom:20px; }
  table.data{ width:100%; border-collapse:collapse; margin:15px 0; }
  table.data td{ padding:5px 0; vertical-align:top; }
  .sign{ width:100%; margin-top:30px; }
  .sign td{ vertical-align:top; }
</style>

<div class="title">SURAT KETERANGAN BELUM MENIKAH</div>
<div class="nomor">Nomor: {{nomor_surat}}</div>

<p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>

<table class="data">
  <tr><td style="width:180px;">Nama Lengkap</td><td>: {{nama}}</td></tr>
  <tr><td>NIK</td><td>: {{nik}}</td></tr>
  <tr><td>Tempat/Tgl Lahir</td><td>: {{tempat_lahir}} / {{tanggal_lahir}}</td></tr>
  <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>
  <tr><td>Agama</td><td>: {{agama}}</td></tr>
  <tr><td>Pekerjaan</td><td>: {{pekerjaan}}</td></tr>
  <tr><td>Alamat</td><td>: {{alamat}}</td></tr>
</table>

<p>Benar yang bersangkutan sampai saat surat ini dibuat <b>berstatus belum menikah</b>.</p>

<table class="data">
  <tr><td style="width:180px;">Tujuan / Instansi</td><td>: {{tujuan_instansi}}</td></tr>
  <tr><td>Keperluan</td><td>: {{keperluan}}</td></tr>
</table>

<p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

<table class="sign">
  <tr>
    <td style="width:60%;"></td>
    <td>Batam, {{tanggal_surat}}<br>Ketua RT {{rt}} / RW {{rw}}<br><br><br><br><b><u>{{nama_rt}}</u></b></td>
  </tr>
</table>',
        'required_json' => ['nomor_surat', 'tanggal_surat', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'rt', 'rw', 'nama_rt', 'tujuan_instansi', 'keperluan']
    ]
];

foreach ($letters as $id => $update) {
    echo "Updating letter ID: $id (" . $update['name'] . ")...\n";
    LetterType::where('id', $id)->update([
        'template_html' => $update['template_html'],
        'required_json' => $update['required_json']
    ]);
}

echo "All templates updated successfully!";
