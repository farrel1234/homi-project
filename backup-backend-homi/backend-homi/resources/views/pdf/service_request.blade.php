<div style="text-align:center; margin-bottom: 25px;">
    <h3 style="margin: 0; font-size: 14pt; text-transform: uppercase; text-decoration: underline; font-weight: bold;">
        {{ $typeName ?? 'SURAT KETERANGAN' }}
    </h3>
    <div style="margin-top: 2px; font-size: 11pt;">
        Nomor: {{ $data['nomor_surat'] ?? '..... / ..... / ..... / ..... / ' . date('Y') }}
    </div>
</div>

<p style="margin-bottom: 15px; text-align: justify;">
    Lurah Belian, Kecamatan Batam Kota, Kota Batam, dengan ini menerangkan :
</p>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; margin-left: 20px; font-size: 12pt;">
    <tr>
        <td style="width: 180px; padding: 2px 0;">Nama Lengkap</td>
        <td style="width: 15px;">:</td>
        <td style="font-weight: bold; text-transform: uppercase;">{{ $reporter ?? '-' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0; font-weight: bold;">N I K</td>
        <td>:</td>
        <td style="font-weight: bold; letter-spacing: 1px;">{{ $data['nik'] ?? '-' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;">Jenis Kelamin</td>
        <td>:</td>
        <td>{{ $data['jenis_kelamin'] ?? '-' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;">Tempat/Tgl Lahir</td>
        <td>:</td>
        <td>{{ $data['tmpt_tgl_lahir'] ?? '-' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;">Agama</td>
        <td>:</td>
        <td>{{ $data['agama'] ?? '-' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;">Kewarganegaraan</td>
        <td>:</td>
        <td>{{ $data['kewarganegaraan'] ?? 'Indonesia' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;">Status Perkawinan</td>
        <td>:</td>
        <td>{{ $data['status_perkawinan'] ?? '-' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;">Pekerjaan</td>
        <td>:</td>
        <td>{{ $data['pekerjaan'] ?? '-' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0; vertical-align: top;">Alamat Sesuai KTP</td>
        <td style="vertical-align: top;">:</td>
        <td>{{ $data['alamat_ktp'] ?? ($data['alamat'] ?? '-') }}</td>
    </tr>

</table>

@if($data['is_layanan'] ?? true)
    {{-- Format Layanan: Lebih simpel dan profesional untuk maintenance/umum --}}
    <p style="text-indent: 40px; margin-bottom: 5px; text-align: justify;">
        Menerangkan bahwa Bapak/Ibu tersebut di atas adalah benar warga kami yang berdomisili di <strong>{{ $data['lokasi_domisili'] ?? 'Perumahan Hawai Garden' }}</strong>. Dengan ini bermaksud mengajukan permohonan layanan:
    </p>
@else
    {{-- Format SKU: Untuk pengajuan yang bersifat formal/usaha --}}
    <p style="text-indent: 40px; margin-bottom: 5px; text-align: justify;">
        Berdasarkan surat pengantar Ketua RT.{{ $data['rt'] ?? '000' }} - RW.{{ $data['rw'] ?? '000' }} Kelurahan Belian, Kecamatan Batam Kota Nomor : {{ $data['nomor_pengantar'] ?? '.....' }} Tanggal {{ $data['tanggal_pengantar'] ?? now()->translatedFormat('d F Y') }} menerangkan bahwa benar mempunyai usaha yang berdomisili/bertempat tinggal di <strong>{{ $data['lokasi_domisili'] ?? 'Perumahan Hawai Garden' }}</strong>. Surat Keterangan ini diberikan untuk keperluan :
    </p>
@endif

<p style="text-align: center; font-weight: bold; margin: 10px 0; text-transform: uppercase; font-size: 13pt;">
    {{ $subject ?? 'PENGURUSAN ADMINISTRASI' }}
</p>

<p style="margin-bottom: 40px; text-align: justify;">
    Demikian surat ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
</p>

<div style="width: 100%; margin-top: 30px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%; text-align: center;">
                <div>Batam, {{ $data['tanggal_surat'] ?? now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-bottom: 60px;">KETUA RT</div>
                
                <div style="font-weight: bold; text-decoration: underline; text-transform: uppercase;">
                    {{ $data['nama_pejabat'] ?? 'KAMARUL AZMI, S.STP' }}
                </div>
                <div style="font-size: 11pt;">
                    NIP: {{ $data['nip_pejabat'] ?? '19860302 120107 1 001' }}
                </div>
            </td>
        </tr>
    </table>
</div>
