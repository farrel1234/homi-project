<style>
    .title { text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; margin-bottom: 2px; text-decoration: underline; }
    .nomor { text-align: center; font-size: 11pt; margin-bottom: 20px; }
    .p-intro { text-indent: 0; margin-bottom: 15px; text-align: justify; line-height: 1.5; }
    .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12pt; }
    .data-table td { padding: 4px 0; vertical-align: top; }
    .data-table .label { width: 180px; }
    .data-table .separator { width: 15px; }
    .content-box { margin: 15px 0; padding: 10px; border: 1px solid #eee; background: #fafafa; }
    .footer-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
    .footer-table td { vertical-align: top; text-align: center; width: 50%; }
</style>

<div class="title">{{ $typeName ?? 'SURAT PERMOHONAN LAYANAN' }}</div>
<div class="nomor">Nomor: {{ $data['nomor_surat'] ?? '..... / ..... / ..... / ..... / ' . date('Y') }}</div>

<p class="p-intro">
    Bersama ini menerangkan bahwa Bapak/Ibu tersebut di bawah ini adalah benar warga kami yang berdomisili di <strong>{{ $data['lokasi_domisili'] ?? 'Perumahan' }}</strong>:
</p>

<table class="data-table">
    <tr>
        <td class="label">Nama Lengkap</td>
        <td class="separator">:</td>
        <td style="font-weight: bold; text-transform: uppercase;">{{ $reporter ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">N I K</td>
        <td class="separator">:</td>
        <td style="font-weight: bold; letter-spacing: 0.5px;">{{ $data['nik'] ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Tempat/Tgl Lahir</td>
        <td class="separator">:</td>
        <td>{{ $data['tmpt_tgl_lahir'] ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Alamat Domisili</td>
        <td class="separator">:</td>
        <td>{{ $data['alamat'] ?? '-' }}</td>
    </tr>
</table>

<p class="p-intro">
    Dengan ini bermaksud mengajukan permohonan layanan/perbaikan dengan rincian sebagai berikut:
</p>

<div class="content-box">
    <table class="data-table" style="margin-bottom: 0;">
        <tr>
            <td class="label" style="width: 150px;">Perihal</td>
            <td class="separator">:</td>
            <td style="font-weight: bold;">{{ $subject ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label" style="width: 150px;">Lokasi Kejadian</td>
            <td class="separator">:</td>
            <td>{{ $place ?? '-' }}</td>
        </tr>
    </table>
</div>

<p class="p-intro" style="margin-top: 20px;">
    Demikian surat permohonan ini dibuat untuk dapat dipergunakan sebagaimana mestinya. Atas perhatian dan bantuannya kami ucapkan terima kasih.
</p>

<table class="footer-table">
    <tr>
        <td>
            <div style="margin-bottom: 10px;">Mengetahui,</div>
            <div style="font-weight: bold; margin-bottom: 65px;">KETUA RT {{ $data['rt'] ?? '' }}</div>
            <div style="font-weight: bold; text-decoration: underline; text-transform: uppercase;">
                {{ $data['nama_pejabat'] ?? '....................' }}
            </div>
        </td>
        <td>
            <div style="margin-bottom: 10px;">Batam, {{ $data['tanggal_surat'] ?? now()->translatedFormat('d F Y') }}</div>
            <div style="font-weight: bold; margin-bottom: 65px;">Hormat Saya,</div>
            <div style="font-weight: bold; text-decoration: underline; text-transform: uppercase;">
                {{ $reporter ?? '....................' }}
            </div>
            <div style="font-size: 10pt;">PEMOHON / PELAPOR</div>
        </td>
    </tr>
</table>
