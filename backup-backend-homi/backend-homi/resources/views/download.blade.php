@extends('layouts.app')

@section('title', 'Unduh Aplikasi Homi')

@section('content')
<div class="container-fluid px-4 py-5">

    {{-- Header --}}
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <div class="mb-4">
                <span style="font-size: 5rem;">📱</span>
            </div>
            <h1 class="fw-bold" style="color: #2F7FA3;">Unduh Aplikasi Homi</h1>
            <p class="text-muted fs-5">Kelola layanan perumahan Anda langsung dari genggaman.<br>
            Tersedia untuk perangkat Android.</p>
        </div>
    </div>

    {{-- Download Card --}}
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                {{-- Card Header --}}
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #1f6f8b, #2F7FA3);">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-android2 me-2"></i>Homi Android App
                    </h4>
                    <small class="opacity-75">Versi Terbaru</small>
                </div>

                <div class="card-body p-4">
                    {{-- Info Versi --}}
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background: #f0f8ff;">
                                <div class="fw-bold text-primary fs-5">1.0</div>
                                <small class="text-muted">Versi</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background: #f0f8ff;">
                                <div class="fw-bold text-primary fs-5">Android</div>
                                <small class="text-muted">Platform</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background: #f0f8ff;">
                                <div class="fw-bold text-primary fs-5">5.0+</div>
                                <small class="text-muted">Min OS</small>
                            </div>
                        </div>
                    </div>

                    {{-- Fitur Unggulan --}}
                    <ul class="list-unstyled mb-4">
                        @foreach([
                            ['icon' => '📄', 'text' => 'Pengajuan surat administrasi online'],
                            ['icon' => '📢', 'text' => 'Pengumuman & notifikasi dari pengurus'],
                            ['icon' => '💳', 'text' => 'Bayar iuran & pantau tagihan'],
                            ['icon' => '📣', 'text' => 'Laporan pengaduan warga'],
                            ['icon' => '👥', 'text' => 'Direktori warga perumahan'],
                        ] as $f)
                        <li class="d-flex align-items-center mb-2">
                            <span class="me-3 fs-5">{{ $f['icon'] }}</span>
                            <span>{{ $f['text'] }}</span>
                        </li>
                        @endforeach
                    </ul>

                    {{-- Notif jika APK belum ada --}}
                    @if(!file_exists(public_path('downloads/homi-app.apk')))
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            File APK belum tersedia. Silakan hubungi administrator untuk mengunggah file instalasi.
                        </div>
                    </div>
                    @endif

                    {{-- Tombol Download --}}
                    <a href="{{ asset('downloads/homi-app.apk') }}"
                       class="btn btn-lg w-100 fw-bold text-white py-3 {{ !file_exists(public_path('downloads/homi-app.apk')) ? 'disabled' : '' }}"
                       style="background: linear-gradient(135deg, #1f6f8b, #2F7FA3); border-radius: 12px;"
                       @if(file_exists(public_path('downloads/homi-app.apk'))) download @endif>
                        <i class="bi bi-download me-2"></i>
                        Unduh APK Sekarang
                    </a>

                    <p class="text-center text-muted mt-3 small">
                        <i class="bi bi-shield-check me-1"></i>
                        Aplikasi aman dan resmi dari pengelola perumahan Anda.
                    </p>
                </div>
            </div>

            {{-- Panduan Instalasi --}}
            <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Cara Instalasi
                    </h5>
                    <ol class="ps-3">
                        <li class="mb-2">Unduh file APK di atas.</li>
                        <li class="mb-2">Buka pengaturan HP → <strong>Keamanan</strong> → Aktifkan <strong>"Sumber Tidak Dikenal"</strong> atau <strong>"Install dari browser"</strong>.</li>
                        <li class="mb-2">Buka file APK yang sudah diunduh dan klik <strong>Install</strong>.</li>
                        <li class="mb-2">Buka aplikasi Homi dan daftar menggunakan kode dari pengelola.</li>
                    </ol>
                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Tip:</strong> Pastikan HP Anda terhubung ke jaringan WiFi yang sama dengan server perumahan saat pertama kali login.
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
