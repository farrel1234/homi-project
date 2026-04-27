# 🏘️ Homi: Smart Multi-Tenant Housing Ecosystem

[![Laravel](https://img.shields.io/badge/Backend-Laravel%2012.x-red.svg)](https://laravel.com)
[![Kotlin](https://img.shields.io/badge/Mobile-Kotlin%20Compose-blue.svg)](https://kotlinlang.org)
[![MySQL](https://img.shields.io/badge/Database-MySQL-blue.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Proprietary-black.svg)](#)

**Homi** adalah platform manajemen residensial modern berbasis **Multi-Tenant & Multi-Database**. Dirancang untuk menjembatani pengelola perumahan dan warga melalui otomasi layanan, transparansi data, dan pengalaman mobile yang premium.

---

## ✨ Value Propositions
- **🚀 Automation First:** Penagihan otomatis, pengingat via WhatsApp, dan layanan surat digital mandiri.
- **🛡️ Multi-Database Security:** Setiap perumahan memiliki database terisolasi untuk menjamin keamanan dan privasi data 100%.
- **🧠 AI-Ready:** Dilengkapi model prediksi Naive Bayes untuk menganalisis risiko tunggakan iuran warga.
- **📱 Premium Mobile Experience:** Aplikasi warga berbasis Android Native (**Jetpack Compose**) dengan antarmuka yang modern dan responsif.

---

## 🛠️ Tech Stack & Architecture

### Backend (The Engine)
- **Framework:** Laravel 12.x (PHP 8.3+)
- **Architecture:** Database-per-Tenant isolation.
- **Key Services:**
    - **PDF Engine:** Pembuatan surat otomatis via Puppeteer/Browsershot.
    - **Notification Hub:** Integrasi FCM (Push), In-App, dan WhatsApp Mock Service.
    - **Job Scheduler:** Redis untuk pemrosesan tugas massal di background.

### Mobile (The Interface)
- **Language:** Kotlin
- **UI Framework:** Jetpack Compose (Modern Declarative UI)
- **Navigation:** Animated AppNavHost untuk transisi antar layar yang mulus.
- **Persistence:** Jetpack DataStore untuk manajemen sesi user.

---

## 📸 Fitur Utama & Progress

| **Aplikasi Warga (Mobile)** | **Dashboard Admin (Web)** |
| :--- | :--- |
| **Interactive Onboarding:** Tutorial interaktif dengan ilustrasi 3D untuk warga baru. | **Tenancy Control:** Sinkronisasi database tenant dan migrasi skema satu pintu. |
| **Smart Billing:** Pembayaran iuran via QRIS dan pelacakan riwayat instan. | **Automated Reminders:** Pemindaian otomatis tagihan jatuh tempo dengan pengingat otomatis. |
| **Portal Dokumen:** Pengajuan surat mandiri (Domisili, dll) dengan output PDF. | **Announcement Master:** Sistem pengumuman eksklusif yang terfokus. |

---

## ⚙️ Cara Instalasi

### 1. Persiapan Backend
```bash
git clone https://github.com/farrel1234/homi-project.git
cd backend-homi
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
```

### 2. Inisialisasi Database
1. Buat database pusat bernama `homi`.
2. Jalankan migrasi khusus central/landlord:
   ```bash
   php artisan migrate --path=database/migrations/landlord
   php artisan db:seed
   ```
3. Inisialisasi tenant pertama:
   ```bash
   php artisan tenant:initialize [kode-perumahan]
   ```

### 3. Sinkronisasi Skema (Tenant-wide)
Setiap kali ada perubahan tabel, jalankan perintah ini untuk sinkronisasi ke seluruh wilayah:
```bash
php artisan homi:tenants-migrate
```

---

## 🏢 Ekosistem Arsitektur
- **Central Context:** Mengelola langganan tenant, akun Super Admin, dan konfigurasi global.
- **Tenant Context:** Menangani operasional harian per wilayah (pengumuman, iuran, pendaftaran warga, staff).

---

## 📜 Dampak Sosial & Bisnis
Homi dikembangkan untuk mengatasi masalah rendahnya kolektibilitas iuran dan kurangnya transparansi pengelolaan dana lingkungan di tingkat RT/RW atau cluster perumahan.

---

**Dikembangkan dengan ❤️ oleh Tim Homi.**
*Proprietary Software - All Rights Reserved.*
