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

        body { background-color: var(--homi-bg); }

        .homi-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--homi-border);
            padding: 1.25rem;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
        }

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
            background: #FFF4ED;
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

        table.homi-table tbody td {
            padding: 14px 12px;
            font-size: 13px;
            color: #374151;
        }

        table.homi-table tbody tr:hover { background: #FFF7F0; }

        .homi-title { font-size: 20px; font-weight: 600; color: #111827; }
        .homi-subtitle { font-size: 13px; color: #6B7280; }
    </style>
</head>

<body class="min-h-screen">

@php
    $navItems = [
        ['label' => 'Dashboard',       'route' => 'admin.dashboard',              'active' => ['admin.dashboard']],
        ['label' => 'Data Warga',      'route' => 'residents.index',              'active' => ['residents.*']],
        ['label' => 'Pengumuman',      'route' => 'announcements.index',          'active' => ['announcements.*']],
        ['label' => 'Pengaduan',       'route' => 'complaints.index',             'active' => ['complaints.*']],
        ['label' => 'Pengajuan Surat', 'route' => 'service-requests.index',       'active' => ['service-requests.*']],

        ['label' => 'Pembayaran',      'route' => 'payments.index',               'active' => ['payments.*']],

        // ✅ sesuai web.php yang bener
        ['label' => 'Notifikasi',      'route' => 'admin.notifications.index',    'active' => ['admin.notifications.*']],
        ['label' => 'Tagihan Iuran',   'route' => 'admin.fees.invoices.index',    'active' => ['admin.fees.invoices.*']],
        ['label' => 'QR Iuran',        'route' => 'admin.fees.qr.index',          'active' => ['admin.fees.qr.*']],
    ];
@endphp

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR DESKTOP ===== --}}
    <aside class="hidden md:flex md:flex-col w-64 bg-[var(--homi-orange)] text-white border-r border-orange-300">

        {{-- Brand --}}
        <div class="p-4 border-b border-orange-300">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/homi-logo.png') }}"
                     alt="HOMI Logo"
                     class="w-10 h-10 rounded-full object-cover bg-white">
                <div class="leading-tight">
                    <div class="font-semibold">HOMI Admin</div>
                    <div class="text-xs text-white/80">Hawai Garden</div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 p-3 space-y-1">
            @foreach($navItems as $item)
                @php
                    $isActive = false;
                    foreach (($item['active'] ?? [$item['route']]) as $p) {
                        if (\Illuminate\Support\Facades\Route::is($p)) { $isActive = true; break; }
                    }

                    // jangan bikin href '#', karena bikin item terlihat "disabled"
                    // tapi tetap aman: kalau route belum ada, jatuh ke /admin/dashboard
                    $href = \Illuminate\Support\Facades\Route::has($item['route'])
                        ? route($item['route'])
                        : route('admin.dashboard');
                @endphp

                <a href="{{ $href }}"
                   class="block px-3 py-2 rounded-lg font-medium text-sm
                          {{ $isActive ? 'bg-white text-[var(--homi-orange)]' : 'text-white hover:bg-orange-200/40' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- User / Logout --}}
        <div class="p-4 border-t border-orange-300">
            @auth
                @php
                    $displayName = Auth::user()->full_name ?? Auth::user()->name ?? 'Admin';
                @endphp

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white text-[var(--homi-blue)] flex items-center justify-center font-bold uppercase">
                        {{ strtoupper(substr($displayName, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold truncate">{{ $displayName }}</div>
                        <div class="text-[11px] text-white/80 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}" class="logout-form mt-3">
                    @csrf
                    <button type="button"
                            class="w-full px-3 py-2 rounded-lg bg-white text-[var(--homi-orange)] font-semibold text-sm hover:bg-orange-50 logout-trigger">
                        Keluar
                    </button>
                </form>
            @endauth
        </div>

    </aside>

    {{-- ===== MOBILE SIDEBAR (DRAWER) ===== --}}
    <div id="sidebar-overlay" class="md:hidden fixed inset-0 bg-black/30 hidden z-40"></div>

    <aside id="mobile-sidebar"
           class="md:hidden fixed top-0 left-0 h-full w-72 bg-[var(--homi-orange)] text-white border-r border-orange-300 z-50
                  transform -translate-x-full transition-transform duration-200 ease-out">

        <div class="p-4 border-b border-orange-300 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/homi-logo.png') }}"
                     alt="HOMI Logo"
                     class="w-10 h-10 rounded-full object-cover bg-white">
                <div class="leading-tight">
                    <div class="font-semibold">HOMI Admin</div>
                    <div class="text-xs text-white/80">Hawai Garden</div>
                </div>
            </div>

            <button id="sidebar-close"
                    class="w-9 h-9 rounded-full bg-white text-[var(--homi-orange)] font-bold">
                ✕
            </button>
        </div>

        <nav class="p-3 space-y-1">
            @foreach($navItems as $item)
                @php
                    $isActive = false;
                    foreach (($item['active'] ?? [$item['route']]) as $p) {
                        if (\Illuminate\Support\Facades\Route::is($p)) { $isActive = true; break; }
                    }

                    $href = \Illuminate\Support\Facades\Route::has($item['route'])
                        ? route($item['route'])
                        : route('admin.dashboard');
                @endphp

                <a href="{{ $href }}"
                   class="block px-3 py-2 rounded-lg font-medium text-sm
                          {{ $isActive ? 'bg-white text-[var(--homi-orange)]' : 'text-white hover:bg-orange-200/40' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        @auth
            @php
                $displayName = Auth::user()->full_name ?? Auth::user()->name ?? 'Admin';
            @endphp

            <div class="p-4 border-t border-orange-300 mt-auto">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white text-[var(--homi-blue)] flex items-center justify-center font-bold uppercase">
                        {{ strtoupper(substr($displayName, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold truncate">{{ $displayName }}</div>
                        <div class="text-[11px] text-white/80 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}" class="logout-form mt-3">
                    @csrf
                    <button type="button"
                            class="w-full px-3 py-2 rounded-lg bg-white text-[var(--homi-orange)] font-semibold text-sm hover:bg-orange-50 logout-trigger">
                        Keluar
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="flex-1 flex flex-col min-h-screen">

        <header class="bg-white border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button id="sidebar-open"
                            class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-full bg-[var(--homi-orange)] text-white"
                            aria-label="Open menu">
                        ☰
                    </button>

                    <div class="leading-tight">
                        <div class="text-sm font-semibold text-gray-800">
                            @yield('page_title', 'Dashboard')
                        </div>
                        <div class="text-[11px] text-gray-500">
                            @yield('page_subtitle', 'Panel Admin HOMI')
                        </div>
                    </div>
                </div>

                @auth
                    @php
                        $displayName = Auth::user()->full_name ?? Auth::user()->name ?? 'Admin';
                    @endphp
                    <div class="hidden sm:flex items-center gap-2">
                        <div class="text-right leading-tight">
                            <div class="text-xs font-semibold text-gray-800">{{ $displayName }}</div>
                            <div class="text-[11px] text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                @endauth
            </div>
        </header>

        <main class="flex-1">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-gray-100 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <p class="text-[11px] text-gray-400 text-center">
                    HOMI &mdash; Sistem Informasi Manajemen Layanan Warga Hawai Garden
                </p>
            </div>
        </footer>
    </div>
</div>

{{-- POPUP LOGOUT --}}
<div id="logout-modal"
     class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/30 backdrop-blur-sm">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const openBtn  = document.getElementById('sidebar-open');
    const closeBtn = document.getElementById('sidebar-close');
    const overlay  = document.getElementById('sidebar-overlay');
    const drawer   = document.getElementById('mobile-sidebar');

    function openDrawer() {
        if (!drawer || !overlay) return;
        drawer.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function closeDrawer() {
        if (!drawer || !overlay) return;
        drawer.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    if (openBtn) openBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);

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
            if (formToSubmit) formToSubmit.submit();
        });
    }

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
