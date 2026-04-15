<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HOMI Admin')</title>
    <link rel="shortcut icon" href="{{ asset('images/homi-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => ['admin.dashboard'], 'icon' => 'dashboard'],
        ['label' => 'Data Warga', 'route' => 'residents.index', 'active' => ['residents.*'], 'icon' => 'residents'],
        ['label' => 'Pengumuman', 'route' => 'announcements.index', 'active' => ['announcements.*'], 'icon' => 'announcements'],
        ['label' => 'Pengaduan', 'route' => 'complaints.index', 'active' => ['complaints.*'], 'icon' => 'complaints'],
        ['label' => 'Pengajuan Surat & Layanan', 'route' => 'service-requests.index', 'active' => ['service-requests.*'], 'icon' => 'services'],
        ['label' => 'Pembayaran', 'route' => 'payments.index', 'active' => ['payments.*'], 'icon' => 'payments'],
        ['label' => 'Notifikasi', 'route' => 'admin.notifications.index', 'active' => ['admin.notifications.*'], 'icon' => 'notifications'],
        ['label' => 'Tagihan Iuran', 'route' => 'admin.fees.invoices.index', 'active' => ['admin.fees.invoices.*'], 'icon' => 'invoices'],
        ['label' => 'QR Iuran', 'route' => 'admin.fees.qr.index', 'active' => ['admin.fees.qr.*'], 'icon' => 'qr'],
    ];

    if (auth()->check() && auth()->user()->isSuperAdmin()) {
        $navItems[] = ['label' => 'Manajemen Staff', 'route' => 'admin.staff.index', 'active' => ['admin.staff.*'], 'icon' => 'residents'];
        $navItems[] = ['label' => 'Manajemen Tenant', 'route' => 'tenants.index', 'active' => ['tenants.*'], 'icon' => 'tenants'];
        $navItems[] = ['label' => 'Permintaan Trial', 'route' => 'tenant-requests.index', 'active' => ['tenant-requests.*'], 'icon' => 'letters'];
    }

    $displayName = auth()->check()
        ? (auth()->user()->full_name ?? auth()->user()->name ?? 'Admin')
        : 'Admin';

    $displayEmail = auth()->check()
        ? (auth()->user()->email ?? '-')
        : '-';
@endphp

<div class="min-h-screen lg:flex">
    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/45 backdrop-blur-[1px] lg:hidden"></div>

    <aside id="app-sidebar"
           class="fixed inset-y-0 left-0 z-50 w-[285px] -translate-x-full border-r border-sky-800/30
                  bg-gradient-to-b from-[#0d5f84] via-[#1f6f8b] to-[#287f9f]
                  text-white shadow-2xl transition-transform duration-200 ease-out lg:static lg:z-auto lg:translate-x-0">
        <div class="flex h-full flex-col">
            <div class="border-b border-white/15 px-5 py-5">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 overflow-hidden rounded-2xl bg-white/90 ring-2 ring-white/40">
                        <img src="{{ asset('images/homi-logo.png') }}" alt="HOMI" class="h-full w-full object-cover">
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.18em] text-white/70">Admin Console</p>
                        <h1 class="text-lg font-bold leading-tight">HOMI</h1>
                        <p class="text-xs text-white/80">{{ session('tenant_name', 'Multi-Tenant System') }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                @foreach($navItems as $item)
                    @php
                        $isActive = false;
                        foreach (($item['active'] ?? [$item['route']]) as $pattern) {
                            if (\Illuminate\Support\Facades\Route::is($pattern)) {
                                $isActive = true;
                                break;
                            }
                        }

                        $href = \Illuminate\Support\Facades\Route::has($item['route'])
                            ? route($item['route'])
                            : route('admin.dashboard');
                    @endphp

                    <a href="{{ $href }}"
                       class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                              {{ $isActive
                                  ? 'bg-white text-[#1f6f8b] shadow-md shadow-sky-900/15'
                                  : 'text-white/90 hover:bg-white/12 hover:text-white' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border
                                     {{ $isActive ? 'border-[#d7eaf3] bg-[#eef6fb] text-[#1f6f8b]' : 'border-white/25 bg-white/10 text-white' }}">
                            @switch($item['icon'])
                                @case('dashboard')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M4 13h7V4H4zM13 20h7v-9h-7zM13 11h7V4h-7zM4 20h7v-5H4z"/></svg>
                                    @break
                                @case('residents')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="3.5"/><path d="M20 8v6M23 11h-6"/></svg>
                                    @break
                                @case('announcements')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M3 11.5V8a2 2 0 0 1 2-2h3l8-3v18l-8-3H5a2 2 0 0 1-2-2v-3.5z"/><path d="M8 18v3"/></svg>
                                    @break
                                @case('complaints')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    @break
                                @case('services')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/></svg>
                                    @break
                                @case('letters')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M4 4h16v16H4z"/><path d="M4 4l8 8 8-8"/></svg>
                                    @break
                                @case('payments')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M16 15h2"/></svg>
                                    @break
                                @case('notifications')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/><path d="M9.5 17a2.5 2.5 0 0 0 5 0"/></svg>
                                    @break
                                @case('invoices')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M6 3h12l2 3v15H4V6z"/><path d="M8 10h8M8 14h8M8 18h5"/></svg>
                                    @break
                                @case('qr')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3z"/><path d="M14 14h3v3h-3zM20 14h1v1h-1zM17 17h4v4h-4z"/></svg>
                                    @break
                                @case('tenants')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17"/></svg>
                                    @break
                            @endswitch
                        </span>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/15 px-4 py-4">
                <div class="rounded-xl border border-white/20 bg-white/10 p-3">
                    <div class="flex items-center gap-3">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white font-bold uppercase text-[#1f6f8b]">
                            {{ strtoupper(substr($displayName, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ $displayName }}</p>
                            <p class="truncate text-[11px] text-white/75">{{ $displayEmail }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}" class="logout-form mt-3 lg:hidden">
                        @csrf
                        <button type="button"
                                class="logout-trigger inline-flex w-full items-center justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-[#1f6f8b] hover:bg-sky-50">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div class="relative flex min-h-screen flex-1 flex-col">
        <header class="sticky top-0 z-30 border-b border-slate-200/90 bg-white/90 backdrop-blur">
            <div class="flex h-16 w-full items-center justify-between px-6 lg:px-10">
                <div class="flex items-center gap-3">
                    <button id="sidebar-open"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 lg:hidden"
                            aria-label="Open menu">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-[1.8]"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                    </button>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.16em] text-slate-400">HOMI Admin</p>
                        <h2 class="text-base font-bold text-slate-900">
                            @yield('page_title', 'Dashboard')
                        </h2>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <div class="hidden sm:block">
                            <select onchange="window.location.href='/admin/tenants/' + this.value + '/switch'" 
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                <option value="central" {{ !session('impersonated_tenant_id') ? 'selected' : '' }}>-- Dashboard Pusat --</option>
                                @foreach(\App\Models\Tenant::where('is_active', true)->orderBy('name')->get() as $t)
                                    <option value="{{ $t->id }}" {{ session('impersonated_tenant_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="hidden rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs text-slate-600 sm:block">
                        @yield('page_subtitle', 'Panel Admin HOMI')
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}" class="logout-form hidden lg:block">
                        @csrf
                        <button type="button"
                                class="logout-trigger inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="w-full px-6 py-6 lg:px-6 xl:px-10 lg:py-8">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-slate-200/90 bg-white/85">
            <div class="w-full px-6 py-3 text-center text-[11px] text-slate-500 lg:px-10">
                HOMI - Sistem Informasi Layanan Warga
            </div>
        </footer>
    </div>
</div>

<div id="logout-modal"
     class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/45 px-4 backdrop-blur-[2px]">
    <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Logout</h3>
        <p class="mt-1 text-sm text-slate-600">
            Anda yakin ingin keluar dari dashboard admin?
        </p>
        <div class="mt-5 flex justify-end gap-2">
            <button id="logout-cancel"
                    class="rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Batal
            </button>
            <button id="logout-confirm"
                    class="rounded-lg bg-[var(--homi-orange)] px-3.5 py-2 text-sm font-semibold text-white hover:bg-[#e67949]">
                Ya, logout
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('sidebar-overlay');
    const sidebar = document.getElementById('app-sidebar');
    const openButton = document.getElementById('sidebar-open');

    function openSidebar() {
        if (!overlay || !sidebar) return;
        overlay.classList.remove('hidden');
        sidebar.classList.remove('-translate-x-full');
    }

    function closeSidebar() {
        if (!overlay || !sidebar) return;
        overlay.classList.add('hidden');
        sidebar.classList.add('-translate-x-full');
    }

    if (openButton) {
        openButton.addEventListener('click', openSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
            closeLogoutModal();
        }
    });

    const logoutModal = document.getElementById('logout-modal');
    const logoutButtons = document.querySelectorAll('.logout-trigger');
    const cancelLogout = document.getElementById('logout-cancel');
    const confirmLogout = document.getElementById('logout-confirm');

    let formToSubmit = null;

    function openLogoutModal(form) {
        if (!logoutModal) return;
        formToSubmit = form;
        logoutModal.classList.remove('hidden');
        logoutModal.classList.add('flex');
    }

    function closeLogoutModal() {
        if (!logoutModal) return;
        logoutModal.classList.add('hidden');
        logoutModal.classList.remove('flex');
        formToSubmit = null;
    }

    logoutButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            openLogoutModal(button.closest('form'));
        });
    });

    if (cancelLogout) {
        cancelLogout.addEventListener('click', closeLogoutModal);
    }

    if (logoutModal) {
        logoutModal.addEventListener('click', function (event) {
            if (event.target === logoutModal) {
                closeLogoutModal();
            }
        });
    }

    if (confirmLogout) {
        confirmLogout.addEventListener('click', function () {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    }
});
</script>

@stack('scripts')
</body>
</html>
