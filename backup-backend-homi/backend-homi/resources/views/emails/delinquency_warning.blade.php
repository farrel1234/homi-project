<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f7f6; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: #1f6f8b; color: #ffffff; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .footer { background: #f9f9f9; padding: 20px; text-align: center; font-size: 12px; color: #888; }
        .button { display: inline-block; padding: 12px 24px; background-color: #1f6f8b; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .risk-badge { display: inline-block; padding: 4px 12px; background-color: #ffebee; color: #c62828; border-radius: 4px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Homi Smart Housing</h1>
        </div>
        <div class="content">
            <h2>Halo, {{ $name }}!</h2>
            <p>{{ $msg }}</p>
            
            @if(isset($data['period']))
                <p><strong>Periode:</strong> {{ $data['period'] }}</p>
            @endif

            <p>Sistem kami memantau ketertiban iuran warga untuk kenyamanan lingkungan bersama. Mohon abaikan pesan ini jika Anda sudah melakukan pembayaran.</p>
            
            <a href="#" class="button">Cek Tagihan Saya</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Homi Project. Developer: Haniip.</p>
            <p>Email ini dikirim secara otomatis oleh sistem prediksi Naive Bayes.</p>
        </div>
    </div>
</body>
</html>
