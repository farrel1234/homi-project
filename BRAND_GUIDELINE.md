# Brand Guideline & UI/UX Notes — Aplikasi HOMI

> Dokumen ini berisi catatan identitas visual, panduan desain, dan rencana marketing aplikasi HOMI.
> Dibuat berdasarkan kode sumber aktual dari repository `homi-project`.

---

## A. Identitas Brand

| | |
|:---|:---|
| **Nama Produk** | HOMI — *Smart Neighborhood Management Portal* |
| **Tagline** | *"Kelola Perumahan Tanpa Batas"* |
| **Kategori** | SaaS (Software as a Service) untuk manajemen perumahan berbasis kecerdasan buatan |
| **Platform** | Web Admin (Laravel) + Mobile App (Android / Kotlin Jetpack Compose) |
| **Kontak** | pbl.512hebat@gmail.com |
| **Institusi** | Politeknik Negeri Batam, Batam, Indonesia |

---

## B. Makna Logo

- **Bentuk:** Logo HOMI adalah ikon rumah minimalis yang disederhanakan, mencerminkan konsep "Home" (rumah) dan komunitas warga.
- **Filosofi:** Logo yang simpel dan geometris merepresentasikan teknologi yang ramah dan mudah diakses oleh semua kalangan, mulai dari pengurus RT/RW hingga warga biasa.
- **Penempatan:** Logo selalu ditampilkan di dalam kotak biru (`#2F7FA3`) dengan ikon berwarna putih di atasnya (efek `brightness-0 invert`) untuk menjaga konsistensi pada berbagai latar belakang.
- **Ukuran Minimum:** Jangan tampilkan logo di bawah ukuran 32x32px agar tetap terbaca.
- **Clear Space:** Selalu berikan jarak minimal setara lebar logo di sekeliling logo agar tidak terkesan sesak.

---

## C. Palet Warna (Color Palette)

> Sumber: `src/style.css` — CSS Theme Variables

| Nama Token | Kode HEX | Fungsi |
|:---|:---|:---|
| **Homi Blue** (Primary) | `#2F7FA3` | Warna utama — tombol, header, elemen interaktif |
| **Homi Dark** (Text / BG) | `#0F172A` | Teks utama, background section gelap |
| **Homi Accent** (Call-to-Action) | `#F97316` | Badge "POPULER", highlight promosi, elemen urgensi |
| **Homi Soft** (Hover / Light) | `#D7EAF3` | Background pill/badge, hover state ringan |
| **Homi Surface** (Background) | `#F8FAFC` | Background halaman utama yang bersih & cerah |
| **Success / Approved** | `#10B981` (Emerald) | Ikon centang verifikasi, status "Lunas" |
| **Danger / Rejected** | `#F43F5E` (Rose) | Ikon peringatan, status "Ditolak" / "Gagal" |
| **Pure White** | `#FFFFFF` | Background kartu, teks di atas background gelap |

### Gradient Utama
```
from: #2F7FA3 (Homi Blue)  →  to: #14B8A6 (Teal-500)
```
Dipakai pada heading hero section dan elemen premium lainnya.

### Hero Background Gradient
```
radial-gradient(circle at 10% 20%, rgba(47, 127, 163, 0.1) 0%, transparent 40%),
radial-gradient(circle at 90% 80%, rgba(249, 115, 22, 0.08) 0%, transparent 40%)
```

---

## D. Tipografi (Typography)

> Sumber: `src/style.css` + `/mobile/homiapps/app/src/main/res/font/`

| Jenis | Font | Platform | Dipakai Di |
|:---|:---|:---|:---|
| **Display / Heading** | `Outfit` (Black / Italic) | Web | Heading besar di Landing Page |
| **Body / UI Web** | `Plus Jakarta Sans` | Web | Paragraf, navigasi, label |
| **Body / UI Mobile** | `Poppins Regular & SemiBold` | Android | Teks umum di Aplikasi Android |
| **Aksen Dekoratif** | `La Belle Aurore` | Android | Font kursif untuk elemen artistik |
| **Sistem Fallback** | `Inter Variable` | Keduanya | Fallback universal jika font utama gagal muat |

### Aturan Tipografi
- **Heading Besar:** Font weight `Black (900)`, letter-spacing `tight / tighter`, style `italic`
- **Sub-heading:** Font weight `Bold (700)`, letter-spacing `normal`
- **Body Text:** Font weight `Medium (500)`, line-height `relaxed` (1.6-1.8) untuk keterbacaan
- **Label / Badge:** Font weight `Bold (700)`, huruf `UPPERCASE`, letter-spacing `widest`

---

## E. Komponen UI (UI Elements)

### Border Radius
| Komponen | Nilai |
|:---|:---|
| Kartu Fitur Utama | `2.5rem` (40px) — sangat melengkung / modern |
| Tombol Utama | `2rem` (32px) |
| Icon Box / Avatar | `0.75rem` (12px) |
| Pill / Badge | `9999px` (full rounded) |

### Efek Visual
| Efek | Implementasi |
|:---|:---|
| **Glassmorphism Header** | `background: rgba(255,255,255,0.7)` + `backdrop-filter: blur(15px)` |
| **Card Shadow (Hover)** | `box-shadow: 0 25px 50px -12px rgba(47, 127, 163, 0.15)` |
| **Pricing Glow** | `box-shadow: 0 0 50px -10px rgba(47, 127, 163, 0.3)` |

### Animasi & Motion
| Nama Animasi | Durasi | Fungsi |
|:---|:---|:---|
| `fadeInUp` | 0.8s ease-out | Elemen muncul dari bawah saat scroll (scroll reveal) |
| `blob` | 7s infinite | Gradien lingkaran organik bergerak di background hero |
| Card Hover | — | `translateY(-8px)` + shadow biru muda (efek "terangkat") |
| Button Click | — | `scale(0.95)` saat diklik (micro-interaction feedback) |
| Transition Global | 0.3s cubic-bezier(0.4, 0, 0.2, 1) | Semua transisi hover/state interaktif |

### Iconography
- Library: **Lucide Icons** (via CDN `unpkg.com/lucide`)
- Ukuran standar ikon utama: `w-8 h-8` (32px)
- Ukuran ikon inline/list: `w-4 h-4` (16px)

---

## F. Navigasi & Layout

### Struktur Halaman Web (Landing Page)
1. **Navbar** — Fixed glass header, tinggi 80px (`h-20`)
2. **Hero Section** — Grid 2 kolom (teks kiri, mockup kanan), background gradient
3. **Features Section** — Grid 4 kolom, kartu dengan ikon dan deskripsi
4. **Advantages Section** — Background gelap (`homi-dark`), layout 2 kolom
5. **Pricing Section** — Grid 3 kolom (Starter / Professional / Elite)
6. **CTA Section** — Full-width, terpusat, 2 tombol aksi
7. **Footer** — Grid 4 kolom, logo + deskripsi + link + kontak

### Breakpoint Responsif
- **Mobile:** Kolom tunggal, font lebih kecil
- **Tablet (md):** 2 kolom untuk grid
- **Desktop (lg):** Layout penuh, mockup hero tampil

---

## G. Rencana Marketing & Target Audiens

### Target Audiens

| Segmen | Profil | Kebutuhan Utama |
|:---|:---|:---|
| **Pengurus RT/RW (Admin)** | Usia 35-55 tahun, tidak selalu tech-savvy | Kemudahan rekap pembayaran & arsip surat tanpa kertas |
| **Warga Perumahan Modern (User)** | Gen Z & Milenial usia 20-40 tahun | Kecepatan layanan, transparansi, dan akses dari HP |
| **Developer / Kontraktor (Enterprise)** | Entitas bisnis properti | Nilai jual "Smart Living" sebagai daya tarik pembeli |

### Proposisi Nilai (Value Proposition)
- ⚡ **Hemat Waktu** — Kurangi waktu administrasi hingga 70%
- 📄 **100% Paperless** — Semua surat dan dokumen dalam format digital
- 🚀 **5X Lebih Cepat** — Respons admin dibanding sistem manual
- 🔒 **Zero Lost Documents** — Tidak ada dokumen yang hilang karena semua tersimpan di cloud

### Struktur Harga (Tiering)
| Paket | Kapasitas | Harga / Tahun | Target |
|:---|:---|:---|:---|
| **Starter** | Hingga 100 unit | Rp 799.000 | RT/RW kecil |
| **Professional** | Hingga 300 unit | Rp 2.400.000 | Cluster perumahan menengah |
| **Elite / Enterprise** | 500+ unit (Unlimited) | Rp 4.800.000 | Perumahan besar / developer |

### Strategi Marketing
1. **Demo Langsung:** Tombol "Minta Demo" di landing page → email langsung ke tim.
2. **Konten Digital:** Short video demo di Instagram/LinkedIn yang menampilkan perbandingan "sebelum vs sesudah" menggunakan HOMI.
3. **Word of Mouth (B2B):** Pendekatan langsung ke pengurus perumahan nyata via referensi.
4. **Upsell Terstruktur:** Paket bertingkat mendorong client untuk upgrade seiring pertumbuhan komunitas.

---

## H. Panduan Tone of Voice

- **Bahasa:** Indonesia (utama) — campuran terminologi teknis dalam Bahasa Inggris untuk konteks profesional.
- **Gaya:** Percaya diri, modern, dan solutif. Bukan birokratis.
- **Contoh Copywriting:**
  - ✅ *"Kelola Perumahan Tanpa Batas."*
  - ✅ *"Sistem Automasi Warga — cepat, transparan, paperless."*
  - ❌ *"Aplikasi ini digunakan untuk keperluan administrasi RT/RW."*

---

*Dokumen ini dibuat pada: April 2026*
*Dibuat dari kode sumber aktual: `homi-project/src/style.css`, `index.html`, dan `/mobile/homiapps/res/`*
