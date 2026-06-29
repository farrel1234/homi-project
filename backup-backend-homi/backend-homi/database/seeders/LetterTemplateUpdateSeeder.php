<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LetterType;

class LetterTemplateUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $template = <<<HTML
<style>
  .title{ text-align:center; font-weight:bold; font-size:14px; text-transform:uppercase; margin-top:6px; text-decoration: underline;}
  .nomor{ text-align:center; margin-top:2px; margin-bottom:14px; }
  table.data{ width:100%; border-collapse:collapse; margin:10px 0 12px; }
  table.data td{ padding:3px 0; vertical-align:top; }
  .sign{ width:100%; margin-top:18px; }
  .sign td{ vertical-align:top; }
</style>

<div class="title">SURAT PERMOHONAN LAYANAN</div>
<div class="nomor">Nomor: {{nomor_surat}}</div>

<p>Dengan hormat,</p>
<p>Saya yang bertanda tangan di bawah ini mengajukan permohonan layanan/perbaikan untuk unit/lokasi sebagai berikut:</p>

<table class="data">
  <tr><td style="width:160px;">Nama Pemohon</td><td>: {{nama_warga}}</td></tr>
  <tr><td>NIK</td><td>: {{nik}}</td></tr>
  <tr><td>Blok / No. Rumah</td><td>: {{alamat}}</td></tr>
  <tr><td>No. HP</td><td>: {{no_telepon}}</td></tr>
  <tr><td>Jenis Layanan</td><td>: {{nama_layanan}}</td></tr>
  <tr><td>Keterangan</td><td>: {{keterangan}}</td></tr>
</table>

<p>Demikian permohonan ini saya sampaikan, atas perhatian dan bantuannya saya ucapkan terima kasih.</p>

<div class="sign">
  <table style="width:100%;">
    <tr>
      <td style="width:50%; text-align:center;">
        Mengetahui,<br>Ketua RT {{rt}}<br><br><br><br>
        ( {{nama_rt}} )
      </td>
      <td style="width:50%; text-align:center;">
        Homi, {{tanggal_surat}}<br>Hormat Saya,<br><br><br><br>
        ( {{nama_warga}} )
      </td>
    </tr>
  </table>
</div>
HTML;

        // Update template untuk type "Surat Permohonan Layanan"
        // Kita cari berdasarkan nama atau ID jika diketahui.
        $type = LetterType::where('name', 'like', '%Layanan%')->first();
        
        if ($type) {
            $type->update([
                'template_html' => $template,
                'required_json' => ['nik', 'no_telepon', 'nama_layanan', 'keterangan']
            ]);
        }
    }
}
