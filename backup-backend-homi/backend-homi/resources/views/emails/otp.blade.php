<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun Homi</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #1f6f8b 0%, #0d5f84 100%);
            padding: 40px 20px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .otp-container {
            background-color: #f8fbff;
            border: 2px dashed #1f6f8b;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 800;
            color: #1f6f8b;
            letter-spacing: 10px;
            margin: 0;
        }
        .validity {
            font-size: 13px;
            color: #666666;
            margin-top: 10px;
        }
        .footer {
            background-color: #f1f4f6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
        .warning {
            background-color: #fff5f5;
            border-left: 4px solid #fc8181;
            padding: 15px;
            margin-top: 30px;
            font-size: 13px;
            color: #c53030;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1f6f8b;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>HOMI PLATFORM</h1>
            <p style="margin-top: 10px; opacity: 0.8;">Smart Living Management</p>
        </div>
        
        <div class="content">
            <div class="greeting">Halo, {{ $name }}!</div>
            <p>Terima kasih telah bergabung dengan Homi. Langkah terakhir untuk mengaktifkan akun Anda adalah dengan memverifikasi kode di bawah ini:</p>
            
            <div class="otp-container">
                <div class="otp-code">{{ $otp }}</div>
                <div class="validity">Kode ini berlaku selama 10 menit</div>
            </div>

            <p>Silakan masukkan kode tersebut ke dalam aplikasi Homi Anda. Jika Anda tidak merasa melakukan pendaftaran ini, silakan abaikan email ini.</p>

            <div class="warning">
                <strong>💡 Penting:</strong> Jangan pernah memberikan kode OTP ini kepada siapapun, termasuk pihak yang mengaku dari Homi. Kami tidak pernah meminta kode OTP Anda.
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Homi Project. All rights reserved.</p>
            <p>Sistem Manajemen Lingkungan Pintar & Transparan</p>
        </div>
    </div>
</body>
</html>
