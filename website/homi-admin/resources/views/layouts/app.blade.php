<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'HOMI Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --homi-blue: #2F79A0;
            --homi-orange: #F8A477;
            --homi-bg: #F5F6F8;
            --homi-border: #E2E8F0;
        }

        body {
            background-color: var(--homi-bg);
        }

        /* ================= CARD / PANEL GLOBAL ================= */
        .homi-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--homi-border);
            padding: 1.25rem; /* 5 */
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
        }

        /* ================= TABEL GLOBAL ================= */
        table.homi-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border: 1px solid var(--homi-border);
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
        }

        table.homi-table thead tr {
            background: #FFF4ED; /* orange muda */
            color: #374151;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        table.homi-table thead th {
            padding: 12px;
            border-bottom: 1px solid var(--homi-border);
        }

        table.homi-table tbody tr {
            border-bottom: 1px solid var(--homi-border);
        }

        table.homi-table tbody tr:last-child {
            border-bottom: none;
        }

        table.homi-table tbody td {
            padding: 14px 12px;
            font-size: 13px;
            color: #374151;
        }

        table.homi-table tbody tr:hover {
            background: #FFF7F0; /* hover orange lembut */
        }

        /* ================= TITLE / SUBTITLE DEFAULT ================= */
        .homi-title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
        }

        .homi-subtitle {
            font-size: 13px;
            color: #6B7280;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- NAVBAR ORANYE --}}
    <header class="bg-[var(--homi-orange)] shadow-sm border-b border-orange-300">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Top bar --}}
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-[var(--homi-blue)] font-bold text-xl">
                        H
                    </div>
                    <div class="flex flex-col leading-tight text-white">
                        <span class="text-sm font-semibold">HOMI Admin</span>
                        <span class="text-[11px] opacity-80">Layanan Warga Hawai Garden</span>
                    </div>
                </div>

                {{-- Menu Desktop --}}
                <nav class="hidden md:flex items-center gap-1 text-sm">

                    @php
                        $navItems = [
                            ['label' => 'Dashboard',   'route' => 'dashboard'],
                            ['label' => 'Data Warga',  'route' => 'residents.index'],
                            ['label' => 'Pengumuman',  'route' => 'announcements.index'],
                            ['label' => 'Pengaduan',   'route' => 'complaints.index'], 
                            ['label' => 'Pengajuan Surat', 'route' => 'letter-requests.index'],
                            ['label' => 'Pembayaran',  'route' => 'payments.index'],
                        ];
                    @endphp

                    @foreach($navItems as $item)
                        @php
                            $isActive = Route::is($item['route']) ||
                                str_starts_with(Route::currentRouteName(), explode('.', $item['route'])[0]);
                        @endphp

                        <a href="{{ route($item['route']) }}"
                           class="px-3 py-2 rounded-full font-medium
                                  {{ $isActive
                                      ? 'bg-white text-[var(--homi-orange)]'
                                      : 'text-white hover:bg-orange-200/40' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                {{-- User + Mobile toggle --}}
                <div class="flex items-center gap-2">

                    {{-- User info desktop --}}
                    @auth
                        <div class="hidden md:flex items-center gap-2 text-white">
                            <div class="leading-tight text-right">
                                <div class="text-xs font-semibold">
                                    {{ Auth::user()->full_name ?? 'Admin' }}
                                </div>
                                <div class="text-[11px] opacity-80">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>

                            <div class="w-9 h-9 rounded-full bg-white text-[var(--homi-blue)] flex items-center justify-center font-bold uppercase">
                                {{ strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 1)) }}
                            </div>
                        </div>

                        {{-- Logout desktop (pakai popup) --}}
                        <form method="POST" action="{{ route('logout') }}" class="hidden md:block logout-form">
                            @csrf
                            <button type="button"
                                    class="ml-1 inline-flex items-center justify-center w-9 h-9 rounded-full bg-white text-[var(--homi-orange)] hover:bg-orange-50 logout-trigger"
                                    title="Keluar">
                                ⟲
                            </button>
                        </form>
                    @endauth

                    {{-- Mobile hamburger --}}
                    <button id="mobile-menu-toggle"
                            class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-full bg-white text-[var(--homi-orange)]"
                            aria-label="Toggle navigation">
                        ☰
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div id="mobile-menu" class="md:hidden hidden pb-3">
                <nav class="pt-2 flex flex-col gap-1 text-sm">

                    @foreach($navItems as $item)
                        @php
                            $isActive = Route::is($item['route']) ||
                                str_starts_with(Route::currentRouteName(), explode('.', $item['route'])[0]);
                        @endphp

                        <a href="{{ route($item['route']) }}"
                           class="px-3 py-2 rounded-lg font-medium
                                  {{ $isActive
                                      ? 'bg-white text-[var(--homi-orange)]'
                                      : 'text-white hover:bg-orange-200/40' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    @auth
                        <div class="border-t border-orange-200 mt-2 pt-2 flex items-center justify-between px-3 text-white">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-white text-[var(--homi-blue)] flex items-center justify-center font-bold uppercase">
                                    {{ strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="leading-tight">
                                    <div class="text-xs font-semibold">
                                        {{ Auth::user()->full_name }}
                                    </div>
                                    <div class="text-[11px] opacity-80">
                                        {{ Auth::user()->email }}
                                    </div>
                                </div>
                            </div>

                            {{-- Logout mobile (pakai popup juga) --}}
                            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                                @csrf
                                <button type="button"
                                        class="text-[11px] px-2 py-1 rounded-full bg-white text-[var(--homi-orange)] logout-trigger">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @endauth

                </nav>
            </div>

        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="flex-1">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-gray-100 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <p class="text-[11px] text-gray-400 text-center">
                HOMI &mdash; Sistem Informasi Manajemen Layanan Warga Hawai Garden
            </p>
        </div>
    </footer>

    {{-- POPUP LOGOUT --}}
    <div id="logout-modal"
         class="fixed inset-0 z-40 hidden items-center justify-center bg-black/30 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-5 mx-4">
            <h2 class="text-sm font-semibold text-gray-800 mb-1">
                Konfirmasi Keluar
            </h2>
            <p class="text-xs text-gray-600 mb-4">
                Apakah Anda yakin ingin keluar dari dashboard HOMI Admin?
            </p>
            <div class="flex justify-end gap-2 text-sm">
                <button id="logout-cancel"
                        class="px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button id="logout-confirm"
                        class="px-3 py-2 rounded-lg bg-[var(--homi-orange)] text-white hover:bg-orange-500">
                    Ya, keluar
                </button>
            </div>
        </div>
    </div>

    {{-- Script: mobile menu + logout popup --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle mobile menu
            const toggle = document.getElementById('mobile-menu-toggle');
            const menu   = document.getElementById('mobile-menu');

            if (toggle && menu) {
                toggle.addEventListener('click', function () {
                    menu.classList.toggle('hidden');
                });
            }

            // Logout popup
            const logoutModal   = document.getElementById('logout-modal');
            const logoutButtons = document.querySelectorAll('.logout-trigger');
            const cancelBtn     = document.getElementById('logout-cancel');
            const confirmBtn    = document.getElementById('logout-confirm');

            let formToSubmit = null;

            logoutButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    formToSubmit = btn.closest('form');
                    if (logoutModal) {
                        logoutModal.classList.remove('hidden');
                        logoutModal.classList.add('flex');
                    }
                });
            });

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    if (logoutModal) {
                        logoutModal.classList.add('hidden');
                        logoutModal.classList.remove('flex');
                    }
                    formToSubmit = null;
                });
            }

            if (confirmBtn) {
                confirmBtn.addEventListener('click', function () {
                    if (formToSubmit) {
                        formToSubmit.submit();
                    }
                });
            }

            // Klik area gelap untuk tutup modal
            if (logoutModal) {
                logoutModal.addEventListener('click', function (e) {
                    if (e.target === logoutModal) {
                        logoutModal.classList.add('hidden');
                        logoutModal.classList.remove('flex');
                        formToSubmit = null;
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
