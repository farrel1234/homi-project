@component('mail::message')
# Verifikasi Akun HOMI

Halo {{ $name }},

Berikut kode OTP untuk verifikasi akun Anda:

@component('mail::panel')
{{ $otp }}
@endcomponent

Kode ini berlaku selama 10 menit.
Jangan berikan kode ini kepada siapa pun.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
