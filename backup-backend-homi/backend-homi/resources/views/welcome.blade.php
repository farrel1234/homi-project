<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HOMI - Elevate Your Neighborhood Management</title>
    <link rel="shortcut icon" href="{{ asset('images/homi-logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        homi: {
                            blue: '#2F7FA3',
                            dark: '#0F172A',
                            accent: '#F97316',
                            soft: '#D7EAF3',
                            surface: '#F8FAFC',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'blob': 'blob 7s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .glass-header {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(47, 127, 163, 0.1) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(249, 115, 22, 0.08) 0%, transparent 40%);
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(47, 127, 163, 0.15);
        }
        .pricing-glow {
            box-shadow: 0 0 50px -10px rgba(47, 127, 163, 0.3);
        }
        .interactive-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .interactive-btn:active {
            transform: scale(0.95);
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</head>
<body class="bg-homi-surface font-sans text-homi-dark antialiased selection:bg-homi-blue selection:text-white">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-header">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3 group cursor-pointer">
                <div class="w-11 h-11 bg-homi-blue rounded-2xl flex items-center justify-center p-2 shadow-xl shadow-blue-200/40 group-hover:rotate-6 transition-transform">
                    <img src="{{ asset('images/homi-logo.png') }}" alt="HOMI" class="w-full h-full object-contain brightness-0 invert">
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-black tracking-tighter text-homi-dark leading-none">HOMI</span>
                </div>
            </div>

            <div class="hidden lg:flex items-center space-x-10 text-sm font-bold text-gray-500 uppercase tracking-widest">
                <a href="#features" class="hover:text-homi-blue transition-colors">Fitur</a>
                <a href="#advantages" class="hover:text-homi-blue transition-colors">Kelebihan</a>
                <a href="#pricing" class="hover:text-homi-blue transition-colors">Harga</a>
                <a href="#trial-form" class="hover:text-homi-blue transition-colors">Coba Sekarang</a>
                <a href="#mobile-app" class="hover:text-homi-blue transition-colors text-homi-blue font-black underline decoration-2 underline-offset-8">Download</a>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="interactive-btn px-6 py-2.5 bg-homi-blue text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-200 hover:shadow-blue-300">
                        Admin Dashboard
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="interactive-btn px-6 py-2.5 border-2 border-homi-blue text-homi-blue rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-homi-blue hover:text-white transition-all">
                        Login Admin
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-40 pb-20 px-6 hero-gradient overflow-hidden">
        <div class="absolute top-20 left-10 w-72 h-72 bg-homi-blue/10 rounded-full blur-[100px] animate-blob"></div>
        <div class="absolute bottom-20 right-10 w-72 h-72 bg-homi-accent/10 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="opacity-0 animate-fade-in-up">

                
                <h1 class="text-5xl lg:text-[5.5rem] font-outfit font-black leading-[0.95] text-homi-dark mb-8 tracking-tighter">
                    Kelola <span class="text-transparent bg-clip-text bg-gradient-to-r from-homi-blue to-teal-500">Perumahan</span> <br/> Tanpa Batas.
                </h1>
                
                <p class="text-xl text-gray-500 mb-12 leading-relaxed max-w-xl font-medium">
                    HOMI mentransformasi cara Anda mengelola perumahan. Dari automasi iuran hingga layanan E-Surat mandiri, semuanya dalam satu genggaman.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center gap-5">
                    <a href="#pricing" class="interactive-btn group w-full sm:w-auto px-10 py-5 bg-homi-blue text-white rounded-3xl font-black text-lg shadow-2xl shadow-blue-200 flex items-center justify-center gap-3">
                        Lihat Penawaran
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="{{ asset('downloads/homi-app.apk') }}" class="interactive-btn w-full sm:w-auto px-10 py-5 bg-homi-accent text-white rounded-3xl font-black text-lg shadow-2xl shadow-orange-200 flex items-center justify-center gap-3">
                        <i data-lucide="download" class="w-5 h-5"></i>
                        Download APK
                    </a>
                    <a href="mailto:pbl.512hebat@gmail.com" class="interactive-btn w-full sm:w-auto px-10 py-5 bg-white text-homi-dark border-2 border-gray-100 rounded-3xl font-black text-lg hover:border-homi-blue transition-colors flex items-center justify-center gap-3">
                        Minta Demo
                    </a>
                </div>


            </div>

            <div class="relative hidden lg:block opacity-0 animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="absolute -inset-10 bg-gradient-to-tr from-homi-blue/10 to-homi-accent/5 rounded-[6rem] blur-3xl -z-10"></div>
                
                <div class="relative flex items-center justify-center">
                    <!-- Dashboard Desktop Backdrop (Admin Console) -->
                    <div class="relative w-[550px] h-[350px] bg-white rounded-[2.5rem] p-2 shadow-2xl border border-slate-100 overflow-hidden -rotate-2 -translate-x-12 translate-y-8 group/dash">
                        <img src="{{ asset('images/homi-dashboard.png') }}" alt="Homi Admin Dashboard" class="w-full h-full object-cover object-top rounded-[2rem] group-hover/dash:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-tr from-slate-900/10 to-transparent"></div>
                    </div>

                    <!-- Phone Frame Floating In Front (Mobile App) -->
                    <div class="absolute translate-x-24 -translate-y-4">
                        <div class="relative w-[220px] h-[440px] bg-slate-900 rounded-[2.5rem] p-3 shadow-[0_50px_100px_-20px_rgba(47,127,163,0.5)] border-[6px] border-slate-800 overflow-hidden group/phone ring-4 ring-white/10">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-5 bg-slate-800 rounded-b-xl z-20"></div>
                            <img src="{{ asset('images/homi-mobile.png') }}" alt="Homi Mobile App" class="w-full h-full object-contain bg-[#2A7596] rounded-[1.8rem] group-hover/phone:scale-105 transition-transform duration-700">
                        </div>
                    </div>
                </div>

                <!-- Floating Badge -->
                <div class="absolute -bottom-6 -right-4 bg-white p-5 rounded-[2rem] shadow-2xl animate-float border border-slate-50 z-30">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shrink-0">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-slate-400 leading-none mb-1 tracking-widest">Efficiency</p>
                            <p class="text-sm font-bold text-slate-900 leading-none italic">Sistem Automasi Warga</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-32 px-6 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-20 gap-8">
                <div class="max-w-xl">
                    <h2 class="text-4xl lg:text-5xl font-outfit font-black tracking-tighter italic mb-4">Fitur Pintar. <br/> Perumahan Makin Cetar.</h2>
                    <p class="text-lg text-gray-500 font-medium leading-relaxed">Dirancang untuk memangkas birokrasi manual dan meningkatkan kepuasan warga.</p>
                </div>
                <div class="hidden lg:block pb-2">
                    <div class="flex -space-x-3">
                        <div class="w-12 h-12 rounded-full border-4 border-white bg-slate-100"></div>
                        <div class="w-12 h-12 rounded-full border-4 border-white bg-slate-200"></div>
                        <div class="w-12 h-12 rounded-full border-4 border-white bg-slate-300"></div>
                        <div class="w-12 h-12 rounded-full border-4 border-white bg-homi-blue flex items-center justify-center text-xs font-black text-white">+8</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="feature-card p-10 bg-slate-50/50 rounded-[2.5rem] border border-transparent hover:bg-white transition-all group">
                    <div class="w-16 h-16 bg-blue-100/50 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-homi-blue group-hover:text-white transition-colors duration-500">
                        <i data-lucide="layout-dashboard" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-xl font-black mb-4 tracking-tight uppercase">Admin Dashboard</h4>
                    <p class="text-sm text-gray-500 leading-relaxed font-medium capitalize">Pantau seluruh aktivitas lingkungan, tunggakan warga, hingga laporan masuk dalam satu layar terpusat.</p>
                </div>
                <div class="feature-card p-10 bg-slate-50/50 rounded-[2.5rem] border border-transparent hover:bg-white transition-all group">
                    <div class="w-16 h-16 bg-amber-100/50 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-homi-accent group-hover:text-white transition-colors duration-500 text-homi-accent">
                        <i data-lucide="scan-line" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-xl font-black mb-4 tracking-tight uppercase">OCR Scan Validation</h4>
                    <p class="text-sm text-gray-500 leading-relaxed font-medium capitalize">Validasi bukti transfer iuran warga secara otomatis menggunakan kecerdasan buatan (OCR Space Integration).</p>
                </div>
                <div class="feature-card p-10 bg-slate-50/50 rounded-[2.5rem] border border-transparent hover:bg-white transition-all group">
                    <div class="w-16 h-16 bg-emerald-100/50 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-500 text-emerald-600">
                        <i data-lucide="file-text" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-xl font-black mb-4 tracking-tight uppercase">Smart E-Surat</h4>
                    <p class="text-sm text-gray-500 leading-relaxed font-medium capitalize">Warga mengajukan surat dari ponsel, admin klik setuju, dan PDF siap didownload secara otomatis.</p>
                </div>
                <div class="feature-card p-10 bg-slate-50/50 rounded-[2.5rem] border border-transparent hover:bg-white transition-all group">
                    <div class="w-16 h-16 bg-rose-100/50 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-rose-500 group-hover:text-white transition-colors duration-500 text-rose-500">
                        <i data-lucide="message-square" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-xl font-black mb-4 tracking-tight uppercase">Aduan Real-time</h4>
                    <p class="text-sm text-gray-500 leading-relaxed font-medium capitalize">Warga bisa melaporkan kerusakan fasilitas dengan foto & lokasi yang langsung diterima dashboard admin.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile App Showcase Section -->
    <section id="mobile-app" class="py-32 px-6 bg-slate-50 overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">
            {{-- Visual HP --}}
            <div class="lg:w-1/2 relative flex justify-center">
                <div class="absolute -top-20 -left-20 w-80 h-80 bg-homi-blue/10 rounded-full blur-[100px] -z-10"></div>
                <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-homi-accent/10 rounded-full blur-[100px] -z-10"></div>
                
                {{-- Mockup HP --}}
                <div class="relative w-[300px] h-[600px] bg-slate-950 rounded-[3rem] p-3 shadow-[0_50px_100px_-20px_rgba(47,127,163,0.3)] border-[8px] border-slate-900 group">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-900 rounded-b-2xl z-20"></div>
                    <div class="w-full h-full overflow-hidden rounded-[2.2rem] bg-[#2A7596]">
                        <img src="{{ asset('images/homi-mobile.png') }}" alt="HOMI Mobile" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700">
                    </div>
                </div>

                {{-- Floating Badge --}}
                <div class="absolute top-1/4 -right-8 bg-white p-4 rounded-2xl shadow-xl border border-slate-100 animate-float">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 text-homi-blue rounded-xl flex items-center justify-center">
                            <i data-lucide="smartphone" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Status</p>
                            <p class="text-xs font-bold text-slate-900">Pembayaran Terverifikasi</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Text Content --}}
            <div class="lg:w-1/2 space-y-10">
                <div class="space-y-4">
                    <span class="px-4 py-1 bg-homi-blue/10 text-homi-blue text-[10px] font-black uppercase tracking-[0.2em] rounded-full">Mobile Experience</span>
                    <h2 class="text-4xl lg:text-5xl font-outfit font-black italic tracking-tighter leading-tight">Aplikasi Warga <br/> Dalam Genggaman.</h2>
                    <p class="text-lg text-gray-500 font-medium leading-relaxed">Berikan kemudahan bagi warga Anda untuk mengurus segala keperluan perumahan hanya dengan satu aplikasi mobile yang ringan dan intuitif.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 bg-amber-50 text-homi-accent rounded-2xl flex items-center justify-center shrink-0">
                            <i data-lucide="credit-card" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-800 uppercase tracking-tight">Bayar Iuran <br/> Tanpa Ribet</p>
                    </div>
                    <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shrink-0">
                            <i data-lucide="file-check-2" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-800 uppercase tracking-tight">E-Surat <br/> Mandiri 24/7</p>
                    </div>
                    <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 bg-blue-50 text-homi-blue rounded-2xl flex items-center justify-center shrink-0">
                            <i data-lucide="megaphone" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-800 uppercase tracking-tight">Info Cluster <br/> Terkini</p>
                    </div>
                    <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center shrink-0">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-800 uppercase tracking-tight">Lapor Keluhan <br/> Sekali Klik</p>
                    </div>
                </div>

                <div class="pt-6">
                    <a href="{{ asset('downloads/homi-app.apk') }}" class="interactive-btn group inline-flex items-center gap-4 px-10 py-5 bg-homi-dark text-white rounded-[2rem] font-black uppercase tracking-[0.2em] text-sm shadow-2xl">
                        <i data-lucide="download" class="w-5 h-5 group-hover:animate-bounce"></i>
                        Download APK Warga
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Advantages Section -->
    <section id="advantages" class="py-32 px-6 bg-homi-dark text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-20">
            <div class="lg:w-1/2">
                <h2 class="text-4xl lg:text-5xl font-outfit font-black italic mb-10 leading-tight tracking-tighter">Kenapa Harus <br/> Menggunakan HOMI?</h2>
                <div class="space-y-10">
                    <div class="flex items-start gap-6 group">
                        <div class="w-14 h-14 rounded-full border border-white/20 flex items-center justify-center shrink-0 group-hover:bg-white group-hover:text-homi-dark transition-all duration-500">
                            <i data-lucide="zap" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h5 class="text-xl font-black uppercase mb-2 tracking-widest text-homi-blue">Operasional Kilat</h5>
                            <p class="text-gray-400 font-medium leading-relaxed capitalize">Hemat waktu hingga 70% untuk urusan administrasi surat-menyurat dan iuran warga setiap bulannya.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-6 group">
                        <div class="w-14 h-14 rounded-full border border-white/20 flex items-center justify-center shrink-0 group-hover:bg-white group-hover:text-homi-dark transition-all duration-500">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h5 class="text-xl font-black uppercase mb-2 tracking-widest text-homi-blue">Zero Error Finance</h5>
                            <p class="text-gray-400 font-medium leading-relaxed capitalize">Pencatatan keuangan otomatis yang transparan, mengurangi resiko kesalahan manusia & fraud.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-6 group">
                        <div class="w-14 h-14 rounded-full border border-white/20 flex items-center justify-center shrink-0 group-hover:bg-white group-hover:text-homi-dark transition-all duration-500">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h5 class="text-xl font-black uppercase mb-2 tracking-widest text-homi-blue">Premium Lifestyle</h5>
                            <p class="text-gray-400 font-medium leading-relaxed capitalize">Meningkatkan nilai profesionalisme perumahan Anda di mata warga maupun calon pembeli properti.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:w-1/2 relative">
                <div class="absolute inset-0 bg-homi-blue/20 rounded-full blur-[120px] -z-10"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="pt-12 space-y-4">
                        <div class="bg-white/5 p-8 rounded-[3rem] border border-white/10 text-center backdrop-blur">
                            <p class="text-4xl font-black font-outfit text-white mb-2 tracking-tighter">100%</p>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest leading-none">Paperless</p>
                        </div>
                        <div class="bg-white/5 p-8 rounded-[3rem] border border-white/10 text-center backdrop-blur">
                            <p class="text-4xl font-black font-outfit text-white mb-2 tracking-tighter">24/7</p>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest leading-none">Security Monitoring</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-white/5 p-8 rounded-[3rem] border border-white/10 text-center backdrop-blur">
                            <p class="text-4xl font-black font-outfit text-white mb-2 tracking-tighter">5X</p>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest leading-none">Faster Responses</p>
                        </div>
                        <div class="bg-white/5 p-8 rounded-[3rem] border border-white/10 text-center backdrop-blur">
                            <p class="text-4xl font-black font-outfit text-white mb-2 tracking-tighter">Zero</p>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest leading-none">Lost Documents</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-32 px-6 bg-white relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-20">
                <h2 class="text-5xl font-outfit font-black italic tracking-tighter mb-6">Investasi Cerdas Untuk <br/> Perumahan Anda.</h2>
                <p class="text-lg text-gray-500 font-medium capitalize">Satu sistem, ribuan manfaat. Pilih paket sesuai jumlah unit rumah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <div class="interactive-btn p-10 rounded-[4rem] bg-slate-50 border border-slate-100 flex flex-col items-center text-center group">
                    <span class="px-4 py-1 bg-white text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-full mb-6 italic">Maksimal 100 Rumah</span>
                    <h3 class="text-xl font-black uppercase mb-2 tracking-widest">Starter</h3>
                    <div class="flex flex-col mb-8">
                        <div class="flex items-baseline justify-center">
                            <span class="text-4xl font-black font-outfit italic tracking-tighter">Rp 1jt</span>
                            <span class="text-gray-400 ml-1 font-bold uppercase text-[8px] tracking-widest">/ Tahun</span>
                        </div>
                        <div class="text-blue-500 text-[10px] font-bold mt-1 italic">Hanya Rp 83rb / bulan</div>
                    </div>
                    <ul class="space-y-4 mb-10 text-xs text-gray-500 font-semibold text-left w-full">
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-emerald-500"></i> Dashboard Admin Cluster</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-emerald-500"></i> Validasi Iuran OCR</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-emerald-500"></i> E-Surat & Pengaduan</li>
                        <li class="flex items-center gap-2 opacity-30"><i data-lucide="x" class="w-3 h-3"></i> Mobile APP (Add-on Only)</li>
                    </ul>
                    <a href="mailto:pbl.512hebat@gmail.com?subject=Minat%20Paket%20Starter" class="mt-auto w-full py-4 bg-white border-2 border-slate-200 text-slate-900 rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] hover:border-homi-blue hover:text-homi-blue transition-all">
                        Pilih Starter
                    </a>
                </div>

                <div class="interactive-btn p-10 rounded-[4rem] bg-homi-blue text-white relative flex flex-col items-center text-center pricing-glow ring-4 ring-blue-50">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-6 py-2 bg-homi-accent text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">POPULER</div>
                    <span class="px-4 py-1 bg-white/10 text-blue-200 text-[10px] font-black uppercase tracking-widest rounded-full mb-6 italic">Maksimal 300 Rumah</span>
                    <h3 class="text-xl font-black uppercase mb-2 tracking-widest italic">Professional</h3>
                    <div class="flex flex-col mb-8 text-white">
                        <div class="flex items-baseline justify-center">
                            <span class="text-4xl font-black font-outfit italic tracking-tighter">Rp 2.5jt</span>
                            <span class="text-blue-100 ml-1 font-bold uppercase text-[8px] tracking-widest">/ Tahun</span>
                        </div>
                        <div class="text-teal-300 text-[10px] font-bold mt-1 uppercase italic">Hemat! Hanya Rp 208rb / bulan</div>
                    </div>
                    <ul class="space-y-4 mb-10 text-xs text-blue-50 font-semibold text-left w-full">
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-teal-300"></i> Semua Fitur Starter</li>
                        <li class="flex items-center gap-2"><i data-lucide="sparkles" class="w-3 h-3 text-amber-300"></i> Mobile APP Eksklusif (APK)</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-teal-300"></i> Support Prioritas WA</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-teal-300"></i> Custom Domain (Optional)</li>
                    </ul>
                    <a href="mailto:pbl.512hebat@gmail.com?subject=Minat%20Paket%20Pro" class="mt-auto w-full py-4 bg-white text-homi-blue rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] shadow-2xl hover:bg-slate-50 transition-all">
                        Ambil Paket Pro
                    </a>
                </div>

                <div class="interactive-btn p-10 rounded-[4rem] bg-homi-dark text-white border border-white/5 flex flex-col items-center text-center group">
                    <span class="px-4 py-1 bg-white/5 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-full mb-6 italic">500+ Rumah / Unlimited</span>
                    <h3 class="text-xl font-black uppercase mb-2 tracking-widest">Elite / Enterprise</h3>
                    <div class="flex flex-col mb-8">
                        <div class="flex items-baseline justify-center">
                            <span class="text-4xl font-black font-outfit italic tracking-tighter">Rp 5jt</span>
                            <span class="text-gray-400 ml-1 font-bold uppercase text-[8px] tracking-widest">/ Tahun</span>
                        </div>
                        <div class="text-homi-blue text-[10px] font-bold mt-1 italic">Hanya Rp 416rb / bulan</div>
                    </div>
                    <ul class="space-y-4 mb-10 text-xs text-gray-400 font-semibold text-left w-full">
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-homi-blue"></i> White Label / Custom Logo</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-homi-blue"></i> Multi-Perumahan Dashboard</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-homi-blue"></i> Dedicated Server Instance</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3 h-3 text-homi-blue"></i> Training & Onboarding On-site</li>
                    </ul>
                    <a href="mailto:pbl.512hebat@gmail.com?subject=Inquiry%20Enterprise%20Elite" class="mt-auto w-full py-4 bg-transparent border-2 border-white/10 text-white rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] hover:bg-white hover:text-homi-dark transition-all">
                        Hubungi Sales
                    </a>
                </div>
            </div>
            
            <div class="mt-16 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300">Butuh solusi untuk 1000+ unit? <a href="#trial-form" class="text-homi-blue underline decoration-2 underline-offset-4">Minta Trial Gratis Sekarang</a></p>
            </div>
        </div>
    </section>

    <!-- Trial Registration Section -->
    <section id="trial-form" class="py-24 px-6 bg-slate-50 border-y border-slate-100">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-[4rem] p-12 shadow-2xl border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-homi-blue/5 rounded-bl-[4rem] -z-10"></div>
                
                <div class="mb-10 text-center">
                    <span class="inline-block px-4 py-1 bg-homi-blue/10 text-homi-blue text-[10px] font-black uppercase tracking-[0.2em] rounded-full mb-4">Coba Gratis 30 Hari</span>
                    <h2 class="text-4xl font-outfit font-black tracking-tighter italic mb-4">Daftarkan Perumahan Anda</h2>
                    <p class="text-gray-500 font-medium capitalize">Isi data di bawah ini, tim kami akan menyiapkan sistem untuk Anda.</p>
                </div>

                <form id="requestTrialForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-4">Nama Perumahan</label>
                        <input type="text" name="name" required placeholder="Contoh: Chelsea Residence" class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] focus:border-homi-blue focus:bg-white transition-all outline-none font-bold text-slate-900">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-4">Nama Pengelola / RT / RW</label>
                        <input type="text" name="manager_name" required placeholder="Nama Lengkap" class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] focus:border-homi-blue focus:bg-white transition-all outline-none font-bold text-slate-900">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-4">Email</label>
                        <input type="email" name="email" required placeholder="email@contoh.com" class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] focus:border-homi-blue focus:bg-white transition-all outline-none font-bold text-slate-900">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-4">Nomor WhatsApp</label>
                        <input type="text" name="phone" required placeholder="0812345678" class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] focus:border-homi-blue focus:bg-white transition-all outline-none font-bold text-slate-900">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-4">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="3" placeholder="Misal: Jumlah rumah 150 unit..." class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] focus:border-homi-blue focus:bg-white transition-all outline-none font-bold text-slate-900"></textarea>
                    </div>

                    <div class="md:col-span-2 mt-4 text-center">
                        <button type="submit" id="submitBtn" class="interactive-btn w-full md:w-64 py-5 bg-homi-blue text-white rounded-[2rem] font-black uppercase tracking-widest text-xs shadow-xl shadow-blue-200">
                            Kirim Permintaan
                        </button>
                    </div>
                </form>

                <div id="successMsg" class="hidden absolute inset-0 bg-white flex flex-col items-center justify-center text-center p-10 z-20">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="check-circle-2" class="w-10 h-10"></i>
                    </div>
                    <h3 class="text-3xl font-outfit font-black italic mb-4">Berhasil Terkirim!</h3>
                    <p class="text-gray-500 font-medium max-w-sm mb-8">Terima kasih atas minat Anda. Kami akan menghubungi Anda melalui WhatsApp atau Email untuk setup dashboard trial dalam waktu maksimal 1x24 jam.</p>
                    <button onclick="location.reload()" class="text-homi-blue font-black uppercase tracking-widest text-[10px] underline underline-offset-8">Kembali</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Pre-Footer CTA -->
    <section class="py-32 px-6 relative overflow-hidden bg-slate-50">
        <div class="absolute inset-0 opacity-40">
            <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="rgba(47, 127, 163, 0.05)"/>
            </svg>
        </div>
        <div class="max-w-4xl mx-auto text-center relative z-10">
            <div class="w-20 h-20 bg-homi-blue rounded-[2.5rem] flex items-center justify-center p-4 shadow-2xl shadow-blue-200/50 mx-auto mb-10">
                <img src="{{ asset('images/homi-logo.png') }}" class="w-full h-full object-contain brightness-0 invert">
            </div>
            <h2 class="text-4xl lg:text-5xl font-outfit font-black italic tracking-tighter mb-10 text-slate-900 leading-tight">Siap Mempersembahkan <br/> Lifestyle Modern untuk Warga?</h2>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-5">
                <a href="mailto:pbl.512hebat@gmail.com" class="interactive-btn group h-16 w-full sm:w-64 bg-homi-dark text-white rounded-[2rem] font-black uppercase tracking-widest text-xs flex items-center justify-center gap-3">
                    Bicara dengan Sales
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <a href="{{ route('admin.login') }}" class="interactive-btn h-16 w-full sm:w-64 border-2 border-slate-950 text-slate-950 rounded-[2rem] font-black uppercase tracking-widest text-xs flex items-center justify-center">
                    Masuk ke Panel Admin
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white pt-24 pb-12 px-6 border-t border-slate-100">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 mb-20">
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-3 mb-8">
                    <img src="{{ asset('images/homi-logo.png') }}" alt="HOMI" class="h-10">
                    <span class="text-2xl font-black uppercase tracking-tighter">HOMI Smart Portal</span>
                </div>
                <p class="text-gray-400 font-medium max-w-sm leading-relaxed capitalize">Solusi SaaS (Software as a Service) terbaik bagi pengelola perumahan yang memimpikan efisiensi, transparansi, dan kemudahan akses layanan.</p>
            </div>
            
            <div>
                <h6 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300 mb-8 leading-none">The System</h6>
                <ul class="space-y-5 text-sm font-bold text-gray-500">
                    <li><a href="#features" class="hover:text-homi-blue transition-colors italic">Fitur Lengkap</a></li>
                    <li><a href="#advantages" class="hover:text-homi-blue transition-colors italic">Kelebihan Platform</a></li>
                    <li><a href="{{ route('admin.login') }}" class="hover:text-homi-blue transition-colors text-homi-blue italic underline decoration-2 underline-offset-4">Developer Login</a></li>
                </ul>
            </div>

            <div>
                <h6 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300 mb-8 leading-none">Contact Us</h6>
                <ul class="space-y-5 text-sm font-bold text-gray-500 underline-offset-4 decoration-2">
                    <li><a href="mailto:pbl.512hebat@gmail.com" class="hover:text-homi-blue transition-colors">pbl.512hebat@gmail.com</a></li>
                    <li><a href="#" class="hover:text-homi-blue transition-colors italic leading-relaxed">Politeknik Negeri Batam, Batam, Indonesia</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto pt-10 border-t border-slate-50 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-300">© 2026 HOMI Project. Advanced Resident Management System.</p>
            <div class="flex items-center gap-6 text-[10px] font-black uppercase tracking-widest text-slate-400 opacity-60">
                <a href="#" class="hover:text-homi-blue italic transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-homi-blue italic transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

    <!-- Lucide init -->
    <script>
        lucide.createIcons();

        // Handle Trial Request Form
        const trialForm = document.getElementById('requestTrialForm');
        const successMsg = document.getElementById('successMsg');
        const submitBtn = document.getElementById('submitBtn');

        if (trialForm) {
            trialForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Disable button
                const originalText = submitBtn.innerText;
                submitBtn.disabled = true;
                submitBtn.innerText = 'MENGIRIM...';
                submitBtn.style.opacity = '0.5';

                const formData = new FormData(trialForm);
                const data = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch('/api/tenant-requests', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        successMsg.classList.remove('hidden');
                        trialForm.classList.add('opacity-0');
                    } else {
                        alert(result.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Gagal menghubungi server. Pastikan koneksi internet Anda aktif.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    submitBtn.style.opacity = '1';
                }
            });
        }
    </script>
</body>
</html>
