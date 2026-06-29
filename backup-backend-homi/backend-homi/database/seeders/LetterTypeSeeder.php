<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LetterType;

class LetterTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            1 => [
                'name' => 'Surat Pengantar',
                'description' => 'Surat pengantar untuk berbagai keperluan warga ke RT/RW.',
                'template_html' => '
<div style="text-align: center; font-family: sans-serif;">
    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT PENGANTAR</h1>
    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>
</div>
<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">
    Yang bertanda tangan di bawah ini Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, dengan ini menerangkan bahwa:
    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">
        <tr><td style="width: 180px;">Nama Lengkap</td><td>: <b>{{nama}}</b></td></tr>
        <tr><td>NIK</td><td>: {{nik}}</td></tr>
        <tr><td>Tempat/Tgl Lahir</td><td>: {{tmpt_tgl_lahir}}</td></tr>
        <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>
        <tr><td>Alamat</td><td>: {{alamat}}</td></tr>
    </table>
    <p style="text-indent: 40px;">Orang tersebut di atas merupakan warga kami di lingkungan RT {{rt}} RW {{rw}} {{nama_perumahan}}, dan berdasarkan pantauan kami yang bersangkutan berkelakuan baik.</p>
    <p>Demikian surat pengantar ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagai kelengkapan administrasi ke <b>{{tujuan_instansi}}</b> guna keperluan: <b>{{keperluan}}</b>.</p>
</div>
<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">
    Batam, {{tanggal_surat}}<br>
    <b>{{pj_label}} {{rt}}</b>
    <br><br><br><br>
    <b><u>{{nama_rt}}</u></b>
</div>',
            ],
            2 => [
                'name' => 'Surat Keterangan Domisili',
                'description' => 'Surat keterangan tempat tinggal warga.',
                'template_html' => '
<div style="text-align: center; font-family: sans-serif;">
    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT KETERANGAN DOMISILI</h1>
    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>
</div>
<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">
    Yang bertanda tangan di bawah ini Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, dengan ini menerangkan bahwa:
    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">
        <tr><td style="width: 180px;">Nama Lengkap</td><td>: <b>{{nama}}</b></td></tr>
        <tr><td>NIK</td><td>: {{nik}}</td></tr>
        <tr><td>Tempat/Tgl Lahir</td><td>: {{tmpt_tgl_lahir}}</td></tr>
        <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>
        <tr><td>Alamat Tinggal</td><td>: {{alamat}}</td></tr>
    </table>
    <p style="text-indent: 40px;">Nama tersebut di atas adalah benar warga kami dan bertempat tinggal di alamat tersebut (Domisili) di wilayah RT {{rt}} RW {{rw}} {{nama_perumahan}}.</p>
    <p>Demikian surat keterangan ini dibuat dengan sebenar-benarnya agar dapat dipergunakan sebagaimana mestinya untuk keperluan: <strong>{{keperluan}}</strong>.</p>
</div>
<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">
    Batam, {{tanggal_surat}}<br>
    <b>{{pj_label}} {{rt}}</b>
    <br><br><br><br>
    <b><u>{{nama_rt}}</u></b>
</div>',
            ],
            3 => [
                'name' => 'Surat Keterangan Kematian',
                'description' => 'Surat keterangan pelaporan kematian warga.',
                'template_html' => '
<div style="text-align: center; font-family: sans-serif;">
    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT KETERANGAN KEMATIAN</h1>
    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>
</div>
<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">
    Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, menerangkan bahwa telah meninggal dunia:
    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">
        <tr><td style="width: 180px;">Nama Almarhum/ah</td><td>: <b>{{nama_alm}}</b></td></tr>
        <tr><td>NIK</td><td>: {{nik_alm}}</td></tr>
        <tr><td>Tempat/Tgl Meninggal</td><td>: {{tmpt_tgl_meninggal_alm}}</td></tr>
        <tr><td>Penyebab</td><td>: {{penyebab}}</td></tr>
        <tr><td>Alamat Terakhir</td><td>: {{alamat_alm}}</td></tr>
    </table>
    <p style="text-indent: 40px;">Demikian surat keterangan kematian ini dibuat dengan sebenar-benarnya berdasarkan pelaporan dari <strong>{{nama_pelapor}}</strong> ({{hubungan}}) agar dapat dipergunakan sebagaimana mestinya.</p>
</div>
<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">
    Batam, {{tanggal_surat}}<br>
    <b>{{pj_label}} {{rt}}</b>
    <br><br><br><br>
    <b><u>{{nama_rt}}</u></b>
</div>',
            ],
            4 => [
                'name' => 'Surat Keterangan Usaha',
                'description' => 'Surat keterangan untuk pembukaan atau kepemilikan usaha.',
                'template_html' => '
<div style="text-align: center; font-family: sans-serif;">
    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT KETERANGAN USAHA</h1>
    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>
</div>
<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">
    Yang bertanda tangan di bawah ini Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, dengan ini menerangkan bahwa:
    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">
        <tr><td style="width: 180px;">Nama Lengkap</td><td>: <b>{{nama}}</b></td></tr>
        <tr><td>NIK</td><td>: {{nik}}</td></tr>
        <tr><td>Tempat/Tgl Lahir</td><td>: {{tmpt_tgl_lahir}}</td></tr>
        <tr><td>Alamat</td><td>: {{alamat}}</td></tr>
    </table>
    <p>Bahwa yang bersangkutan benar memiliki/menjalankan usaha dengan rincian berikut:</p>
    <table style="width: 100%; margin-left: 20px;">
        <tr><td style="width: 180px;">Nama Usaha</td><td>: <b>{{nama_usaha}}</b></td></tr>
        <tr><td>Bidang Usaha</td><td>: {{bidang_usaha}}</td></tr>
        <tr><td>Alamat Usaha</td><td>: {{alamat_usaha}}</td></tr>
    </table>
    <p>Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya untuk keperluan: <b>{{keperluan}}</b>.</p>
</div>
<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">
    Batam, {{tanggal_surat}}<br>
    <b>{{pj_label}} {{rt}}</b>
    <br><br><br><br>
    <b><u>{{nama_rt}}</u></b>
</div>',
            ],
            5 => [
                'name' => 'Surat Keterangan Belum Menikah',
                'description' => 'Surat pernyataan status pernikahan warga.',
                'template_html' => '
<div style="text-align: center; font-family: sans-serif;">
    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT KETERANGAN BELUM MENIKAH</h1>
    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>
</div>
<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">
    Yang bertanda tangan di bawah ini Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, dengan ini menerangkan bahwa:
    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">
        <tr><td style="width: 180px;">Nama Lengkap</td><td>: <b>{{nama}}</b></td></tr>
        <tr><td>NIK</td><td>: {{nik}}</td></tr>
        <tr><td>Tempat/Tgl Lahir</td><td>: {{tmpt_tgl_lahir}}</td></tr>
        <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>
        <tr><td>Agama</td><td>: {{agama}}</td></tr>
        <tr><td>Alamat</td><td>: {{alamat}}</td></tr>
    </table>
    <p style="text-indent: 40px;">Berdasarkan keterangan yang ada pada kami dan sepanjang sepengetahuan kami, nama tersebut di atas adalah benar warga kami yang sampai saat ini <strong>Belum Pernah Menikah</strong> (Jejaka/Perawan).</p>
    <p>Demikian surat keterangan ini dibuat dengan sebenar-benarnya agar dapat dipergunakan sebagaimana mestinya untuk keperluan: <strong>{{keperluan}}</strong> ke <strong>{{tujuan_instansi}}</strong>.</p>
</div>
<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">
    Batam, {{tanggal_surat}}<br>
    <b>{{pj_label}} {{rt}}</b>
    <br><br><br><br>
    <b><u>{{nama_rt}}</u></b>
</div>',
            ],
        ];

        foreach ($items as $id => $data) {
            LetterType::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'template_html' => trim($data['template_html'])
                ]
            );
        }
    }
}
