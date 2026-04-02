<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - HOMI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden">
    <div class="relative min-h-screen bg-[radial-gradient(circle_at_12%_10%,rgba(240,138,93,0.26),transparent_36%),radial-gradient(circle_at_88%_20%,rgba(31,111,139,0.2),transparent_34%),linear-gradient(180deg,#f8fbfd_0%,#edf4f9_100%)]">
        <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 items-stretch px-4 py-6 sm:px-6 lg:grid-cols-2 lg:gap-10 lg:px-8 lg:py-8">
            <section class="order-2 flex items-center lg:order-1">
                <div class="w-full rounded-3xl border border-slate-200 bg-white/92 p-6 shadow-[0_20px_40px_rgba(16,42,67,0.12)] backdrop-blur-sm sm:p-8">
                    <div class="mb-7 flex items-center gap-3">
                        <div class="h-14 w-14 overflow-hidden rounded-2xl bg-slate-50 ring-2 ring-slate-100">
                            <img src="{{ asset('images/homi-logo.png') }}" alt="HOMI" class="h-full w-full object-cover">
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Admin Console</p>
                            <h1 class="text-2xl font-bold text-slate-900">Selamat Datang</h1>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-slate-900">Masuk ke Dashboard HOMI</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Gunakan akun admin terdaftar untuk mengelola layanan warga.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label for="tenant_id" class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Pilih Tenant / Perumahan</label>
                            <select id="tenant_id" name="tenant_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 focus:border-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-100">
                                <option value="" disabled selected>-- Pilih Perumahan --</option>
                                <option value="0" {{ old('tenant_id') == '0' ? 'selected' : '' }} class="font-bold text-sky-700">
                                    [Pusat] Manajemen Sistem Global
                                </option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Email</label>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="contoh: admin@homi.test"
                                   required
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-100">
                        </div>

                        <div class="space-y-1.5">
                            <label for="password" class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Kata Sandi</label>
                            <input id="password"
                                   type="password"
                                   name="password"
                                   placeholder="Masukkan kata sandi"
                                   required
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-100">
                        </div>

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-[var(--homi-orange)] to-[#e57949] px-4 py-3 text-sm font-bold text-white shadow-[0_12px_26px_rgba(240,138,93,0.32)] hover:opacity-95">
                            Login Admin
                        </button>
                    </form>

                    <div class="mt-4 text-center">
                        <a href="{{ route('portal') }}"
                           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#2F7FA3] transition-colors">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 fill-none stroke-current stroke-2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            Kembali ke Landing Page
                        </a>
                    </div>
                </div>
            </section>

            <section class="order-1 mb-4 flex items-center lg:order-2 lg:mb-0">
                <div class="relative w-full overflow-hidden rounded-3xl border border-white/55 bg-white/70 p-6 shadow-[0_16px_32px_rgba(16,42,67,0.1)] backdrop-blur-sm sm:p-8">
                    <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-orange-200/60 blur-2xl"></div>
                    <div class="absolute -bottom-20 -left-16 h-56 w-56 rounded-full bg-sky-200/60 blur-2xl"></div>

                    <div class="relative">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">HOMI Platform</p>
                        <h3 class="mt-2 text-3xl font-bold leading-tight text-slate-900">
                            Kelola layanan warga lebih cepat dan rapi.
                        </h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Dashboard admin dirancang agar petugas mudah memproses pengumuman, pengaduan,
                            pengajuan surat, dan pembayaran iuran dengan alur yang jelas.
                        </p>

                        <div class="mt-7 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white/85 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Monitoring</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">Ringkasan data warga dan transaksi</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white/85 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Aksi Cepat</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">Approve, reject, dan kirim notifikasi</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white/85 p-4 sm:col-span-2">
                                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Responsif</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">Nyaman dipakai di desktop maupun mobile admin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
