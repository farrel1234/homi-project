# HOMI PROJECT — Panduan Konteks untuk AI Assistant

> Dokumen ini berisi semua konteks penting proyek Homi agar AI assistant (Antigravity / Claude) dapat bekerja secara konsisten, tepat, dan efisien tanpa perlu penjelasan berulang.

---

## 1. Identitas Proyek

- **Nama Proyek**: Homi — Platform Manajemen Lingkungan Perumahan Pintar
- **Deskripsi**: Digitalisasi layanan perumahan — sistem pengelolaan administrasi warga berbasis digital, mencakup pengajuan surat, pengaduan, iuran/pembayaran, direktori warga, dan notifikasi.
- **Tujuan Akademik**: Studi kasus untuk skripsi dengan judul "IMPLEMENTASI ALGORITMA NAIVE BAYES DALAM MENENTUKAN PRIORITAS PENANGANAN TUNGGAKAN IURAN WARGA PADA APLIKASI HOMI".

---

## 2. Arsitektur Sistem

### Backend
- **Framework**: Laravel (PHP 8.x)
- **Arsitektur**: Multi-Tenancy (Multi-Database) — setiap perumahan punya database sendiri
- **Database**: MySQL
- **Auth**: Laravel Sanctum (token-based, `expiration = null` — sesi tidak kadaluarsa)
- **PDF**: Dibuat menggunakan `dompdf` via template Blade di `LetterTypeSeeder.php`
- **Machine Learning**: Native PHP, tidak menggunakan library eksternal
- **Email**: Laravel Mail (SMTP) dengan template Blade custom di `resources/views/emails/`

### Mobile
- **Bahasa**: Kotlin
- **UI Framework**: Jetpack Compose
- **Navigasi**: `AppNavHostAnimated.kt` (single NavHost)
- **State Management**: `StateFlow` + `ViewModel`
- **Penyimpanan Sesi**: `TokenStore.kt` menggunakan Jetpack DataStore

### Admin Dashboard
- **Teknologi**: Laravel Blade (server-side rendered)

---

## 3. Struktur Folder Penting

```
homi-project/
├── backup-backend-homi/backend-homi/    ← Backend Laravel (AKTIF)
│   ├── app/
│   │   ├── Console/Commands/
│   │   │   ├── MlTrainDelinquencyNb.php  ← Training Naive Bayes
│   │   │   ├── MlScoreDelinquencyNb.php  ← Scoring/Prediksi semua warga
│   │   │   └── NotifyDelinquencyRisk.php ← Notifikasi warga berisiko
│   │   ├── Services/
│   │   │   ├── DelinquencyNaiveBayes.php ← Core algoritma NB (predict, train)
│   │   │   └── DelinquencyCheckService.php
│   │   └── Mail/
│   │       └── OtpMail.php               ← Email OTP (sudah premium HTML)
│   ├── database/seeders/
│   │   └── LetterTypeSeeder.php           ← Template HTML surat (Clean Pro design)
│   └── resources/views/emails/
│       └── otp.blade.php                  ← Template email OTP (Full HTML, bukan markdown)
│
└── mobile/homiapps/                      ← Android App (Kotlin + Jetpack Compose)
    └── app/src/main/java/com/example/homi/
        ├── navigation/
        │   └── AppNavHostAnimated.kt      ← Semua routing navigasi aplikasi
        ├── data/local/
        │   └── TokenStore.kt             ← Persistensi sesi (DataStore)
        ├── ui/screens/
        │   └── Beranda.kt                ← Dashboard utama warga
        └── ui/viewmodel/
            └── AuthViewModel.kt          ← Logic autentikasi
```

---

## 4. Keputusan Desain yang Sudah Ditetapkan

| Aspek | Keputusan | Alasan |
|---|---|---|
| Desain PDF Surat | **Clean Pro** (minimalis, tanpa banner biru/premium) | Permintaan pengguna, terlihat lebih resmi |
| Email OTP | **Full HTML custom** (bukan Laravel Markdown) | Branding premium, layout lebih menarik |
| Sesi Login Mobile | **Persistent (tidak logout otomatis)** | User tidak perlu login ulang jika belum logout |
| Tutorial Onboarding | **Tampil sekali saja** (saat pertama daftar) | Tidak mengganggu pengguna yang sudah paham |
| Blokir Layanan | **`HomiDialog`** dengan ikon `Outlined.ReportProblem` | Menandatanguhkan layanan jika warga menunggak |

---

## 5. Algoritma Naive Bayes — Ringkasan Teknis

### Fitur (Input)
| Fitur | Tipe | Keterangan |
|---|---|---|
| `unpaid_3m` | Bernoulli (0/1) | Ada tunggakan belum bayar di 3 bulan terakhir |
| `late_3m` | Bernoulli (0/1) | Ada pembayaran terlambat di 3 bulan terakhir |
| `avg_late_6m` | Bernoulli (0/1) | Rata-rata hari telat > 0 dalam 6 bulan |
| `last_status` | Kategorikal | Status invoice terakhir: `paid_on_time/paid_late/unpaid` |
| `amount_bucket` | Kategorikal | Nominal iuran: `<=50k / 50-150k / >150k` |
| `pekerjaan` | Kategorikal | Jenis pekerjaan warga dari profil |
| `house_type` | Kategorikal | Tipe rumah: Tipe 36/45/60/72 |

### Output
- `predicted_delinquent`: 0 (Tertib) atau 1 (Berisiko Menunggak)
- `probability_score`: Nilai 0.0 - 1.0 hasil softmax

### Alur Kerja
1. **Training**: `php artisan ml:train-delinquency-nb` → Simpan model ke tabel `ml_nb_models`
2. **Scoring**: `php artisan ml:score-delinquency-nb` → Simpan skor ke tabel `payment_risk_scores`
3. **Notifikasi**: `php artisan notify:delinquency-risk` → Push notification FCM ke HP warga
4. **Enforcement**: Jika warga coba ajukan surat & `isBlockedByArrears = true` → Muncul dialog blokir

---

## 6. Konvensi Koding

### PHP (Laravel)
- Gunakan `$this->info(...)` untuk log output di Artisan commands
- Gunakan Laplace Smoothing (`$alpha = 1.0`) di semua perhitungan probabilitas
- Template surat menggunakan format `{{PLACEHOLDER}}` (huruf kapital, kurung kurawal ganda)
- Sinkronisasi template antar tenant menggunakan skrip `scratch/sync_templates.php`

### Kotlin (Jetpack Compose)
- Semua warna menggunakan token: `BlueMain`, `BlueDark`, `AccentOrange`, dll.
- Font: `PoppinsSemi`, `PoppinsReg`, `SuezOne`
- Navigasi: gunakan `popUpTo(...) { inclusive = true }` saat berpindah dari splash/login
- `TokenStore` adalah satu-satunya sumber kebenaran untuk data sesi pengguna

---

## 7. Catatan Penting

- **Jangan ubah** desain PDF ke format "Premium" (dengan banner biru) — pengguna sudah meminta dikembalikan ke Clean Pro
- **Jangan set** `initial = false` pada `hasSeenOnboardingFlow.collectAsState()` di `Beranda.kt` — ini menyebabkan tutorial muncul berulang kali
- **Token Sanctum** di backend dikonfigurasi `expiration = null` — token tidak pernah expired secara otomatis
- Multi-tenancy: setiap perubahan seeder harus disinkronkan ke semua tenant via `sync_templates.php`

---

## 8. Perintah Terminal yang Sering Digunakan

```bash
# Backend Laravel
cd c:\laragon\www\homi-project\backup-backend-homi\backend-homi

php artisan ml:train-delinquency-nb        # Training model NB
php artisan ml:score-delinquency-nb        # Scoring semua warga bulan ini
php artisan notify:delinquency-risk        # Kirim notifikasi ke warga berisiko
php artisan db:seed --class=LetterTypeSeeder  # Seed ulang template surat
php scratch/sync_templates.php             # Sinkronisasi template ke semua tenant
```

---

*Dokumen ini dibuat oleh Antigravity untuk membantu AI assistant bekerja lebih efisien pada proyek Homi.*
*Terakhir diperbarui: April 2026*
