# LAPORAN PENGUJIAN PERANGKAT LUNAK: HOMI PLATFORM

Laporan pengujian sistematis ini disusun untuk memenuhi kebutuhan dokumentasi pengujian kualitas perangkat lunak pada aplikasi PBL **HOMI (Sistem Informasi Layanan Warga)**.

---

## A. Pendahuluan/Tujuan Pengujian
Tujuan utama dari pengujian ini adalah:
1. Menjamin stabilitas fitur utama pada panel administrasi HOMI.
2. Memverifikasi isolasi data multi-tenant (perumahan Hawaii Garden vs tenant lainnya).
3. Menguji kepatuhan API backend terhadap skema data dan performa dasar di bawah beban ringan.
4. Menemukan bug, bottleneck performa, atau celah konfigurasi sebelum masuk fase produksi.

---

## B. Tools yang Digunakan
1. **Playwright**: E2E testing untuk browser web admin, simulasi interaksi pengguna, dan penangkapan screenshot otomatis.
2. **Postman / Newman**: Verifikasi API endpoint (skema, token auth, dan isolasi tenant).
3. **k6**: Load testing performa API untuk memantau latensi dan kestabilan concurrency server lokal.
4. **MySQL 8.4 (Laragon)**: Database storage utama untuk multi-tenancy.

---

## C. Skenario Pengujian
Pengujian mencakup:
- **E2E Admin Login & Navigation**: Pengujian alur login admin perumahan (Hawaii Garden), serta verifikasi menu Data Warga, Iuran, Pembayaran, Pengumuman, Pengaduan, dan Prioritas SAW.
- **API Functional & Security**: Verifikasi token bearer auth, serta perlindungan privasi tenant (penolakan request tanpa header `X-Tenant-Code` atau dengan header invalid).
- **Load Testing**: Pengujian ketahanan endpoint publik (`/api/ping` dan `/api/tenants`) dengan 5 virtual users selama 30 detik.

---

## D. Langkah-langkah Pengujian
1. Konfigurasi database `.env` diarahkan ke Laragon MySQL.
2. Jalankan migrasi pusat dan migrasi tenant menggunakan `php artisan migrate` dan `php artisan homi:tenants-migrate`.
3. Inisialisasi tenant Hawaii Garden dengan `php artisan tenant:initialize hawaii-garden` dan sinkronkan admin dengan `php artisan homi:sync-admins`.
4. Jalankan server lokal:
   - Backend API & Web: `php artisan serve`
   - Assets compiler: `npm run dev`
5. Jalankan test suite Playwright: `npx playwright test`
6. Jalankan collection API menggunakan Newman: `npx newman run tests/api/HOMI_API_Testing.postman_collection.json -e tests/api/HOMI_API_Environment.postman_environment.json`
7. Jalankan load test k6: `k6 run tests/performance/load-test.js`

---

## E. Hasil Pengujian

### E.1 Tabel Test Case Pengujian

| ID Test Case | Fitur | Tools | Skenario | Langkah Pengujian | Expected Result | Actual Result | Status | Screenshot |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **TC001** | Auth Admin | Playwright | Login admin berhasil | Masukkan tenant Hawaii Garden, email `admin@test.id`, password `password`, klik submit. | Redirect ke `/admin/dashboard` dengan sesi yang valid. | Berhasil masuk dan redirect ke halaman dashboard. | **PASSED** | [TC001_login_admin_success.png](docs/testing/screenshots/e2e/TC001_login_admin_success.png) |
| **TC002** | Auth Admin | Playwright | Login gagal jika password salah | Masukkan tenant Hawaii Garden, email `admin@test.id`, password salah, klik submit. | Tetap di halaman login dan muncul pesan kesalahan/error. | Halaman login menampilkan validasi error, login ditolak. | **PASSED** | [TC002_login_failed_wrong_password.png](docs/testing/screenshots/e2e/TC002_login_failed_wrong_password.png) |
| **TC003** | Dashboard | Playwright | Memuat halaman Dashboard | Login sukses dan tunggu halaman memuat elemen dashboard. | Menampilkan widget ringkasan data perumahan. | Halaman dashboard termuat dengan informasi widget data perumahan. | **PASSED** | [TC003_dashboard_loaded.png](docs/testing/screenshots/e2e/TC003_dashboard_loaded.png) |
| **TC004** | Warga | Playwright | Memuat halaman Data Warga | Klik menu "Data Warga" pada sidebar. | Menampilkan tabel warga khusus perumahan terpilih. | Data warga perumahan Hawaii Garden berhasil dimuat di tabel. | **PASSED** | [TC004_resident_page_loaded.png](docs/testing/screenshots/e2e/TC004_resident_page_loaded.png) |
| **TC005** | Tagihan | Playwright | Memuat halaman Tagihan Iuran | Klik menu "Tagihan Iuran" pada sidebar. | Menampilkan riwayat invoice dan status pembayaran. | Halaman memuat invoice tagihan warga perumahan. | **PASSED** | [TC005_invoice_page_loaded.png](docs/testing/screenshots/e2e/TC005_invoice_page_loaded.png) |
| **TC006** | Pembayaran | Playwright | Memuat halaman Pembayaran | Klik menu "Pembayaran" pada sidebar. | Menampilkan daftar transaksi pembayaran warga. | Modul pembayaran termuat dengan list invoice yang dibayar. | **PASSED** | [TC006_payment_page_loaded.png](docs/testing/screenshots/e2e/TC006_payment_page_loaded.png) |
| **TC007** | Pengumuman | Playwright | Memuat halaman Pengumuman | Klik menu "Pengumuman" pada sidebar. | Menampilkan list pengumuman aktif dan opsi tambah baru. | Halaman pengumuman ter-render dengan benar. | **PASSED** | [TC007_announcement_page_loaded.png](docs/testing/screenshots/e2e/TC007_announcement_page_loaded.png) |
| **TC008** | Pengaduan | Playwright | Memuat halaman Pengaduan | Klik menu "Pengaduan" pada sidebar. | Menampilkan daftar aspirasi/keluhan dari warga perumahan. | List keluhan warga termuat di halaman. | **PASSED** | [TC008_complaint_page_loaded.png](docs/testing/screenshots/e2e/TC008_complaint_page_loaded.png) |
| **TC009** | SAW Prioritas | Playwright | Memuat halaman prioritas tunggakan | Klik menu "Prioritas Tunggakan" pada sidebar. | Menampilkan peringkat ranking SAW prioritas tunggakan warga jika tersedia. | Halaman prioritas tunggakan menampilkan skor bobot alternatif SAW. | **PASSED** | [TC009_saw_priority_page_loaded.png](docs/testing/screenshots/e2e/TC009_saw_priority_page_loaded.png) |
| **TC010** | Multi-Tenant | Playwright | Keamanan data multi-tenant | Amati data perumahan yang tampil di halaman dashboard/warga. | Hanya menampilkan data Hawaii Garden, tidak ada data tenant lain. | Berdasarkan skenario pengujian yang dilakukan, data yang tampil berada dalam scope tenant Hawaii Garden dan tidak ditemukan indikasi data tenant lain muncul selama pengujian berlangsung. | **PASSED** | [TC010_multi_tenant_hawaii_garden.png](docs/testing/screenshots/e2e/TC010_multi_tenant_hawaii_garden.png) |

---

### E.2 Bukti Eksekusi Tools
Bukti eksekusi nyata berupa log keluaran terminal telah disimpan dan di-render secara visual:

1. **Playwright E2E Log & Visual**:
   * Log Tekstual: [docs/testing/logs/playwright-result.txt](docs/testing/logs/playwright-result.txt)
   * Tangkapan Layar Terminal: [docs/testing/screenshots/tools/playwright_result.png](docs/testing/screenshots/tools/playwright_result.png)

2. **Newman API Testing Log & Visual**:
   * Log Tekstual: [docs/testing/logs/api-test-result.txt](docs/testing/logs/api-test-result.txt)
   * Tangkapan Layar Terminal: [docs/testing/screenshots/tools/api_test_result.png](docs/testing/screenshots/tools/api_test_result.png)

3. **k6 Performance Testing Log & Visual**:
   * Log Tekstual: [docs/testing/logs/k6-result.txt](docs/testing/logs/k6-result.txt)
   * Tangkapan Layar Terminal: [docs/testing/screenshots/tools/k6_result.png](docs/testing/screenshots/tools/k6_result.png)

---

### E.3 Ringkasan Hasil Pengujian
* **Fungsionalitas Visual E2E**: 100% Passed. Semua modul web admin dapat terbuka dan termuat dengan data yang valid.
* **Isolasi API & Proteksi Tenant**: 100% Passed. Newman memvalidasi 13 asersi fungsional & keamanan tanpa ada kegagalan. Request tanpa token atau dengan ID tenant invalid ditolak dengan benar oleh middleware.
* **Beban API k6**: Sukses dilakukan. Hasil performa di k6-result.txt menunjukkan respon rata-rata di bawah 300ms dengan tingkat kegagalan request 0% setelah Redis dinonaktifkan dari konfigurasi cache development local.

---

## F. Temuan Bug/Issue
1. **Ketergantungan Redis secara Default (Configuration Bug)**:
   - **Deskripsi**: Di file `.env`, parameter `CACHE_STORE` diset ke `redis` secara default. Apabila local machine tidak menjalankan Redis server (port 6379), Laravel langsung melempar error **500 Internal Server Error** saat memanggil endpoint API maupun web.
   - **Dampak**: Menghalangi startup aplikasi bagi pengembang local baru yang belum memiliki setup Redis.
2. **Keterbatasan Concurrency PHP CLI Server (Performance Bottleneck)**:
   - **Deskripsi**: Hasil load testing dengan k6 (5 Virtual Users) menunjukkan kerentanan bottleneck pada server development default `php artisan serve` yang bersifat single-threaded apabila dibebani request paralel padat.
   - **Dampak**: API tidak responsif atau time out ketika diakses oleh beberapa user secara bersamaan.
3. **Endpoint API Tidak Terimplementasi (Missing Routes)**:
   - **Deskripsi**: Skenario API untuk *Get dashboard summary* dan *Get SAW priority results* tidak ditemukan di route list `api.php`. Fitur-fitur ini hanya diimplementasikan sebagai web views / Blade templates di `web.php`.

---

## G. Analisis dan Rekomendasi Perbaikan
1. **Peningkatan Skalabilitas Dev Environment**:
   - *Rekomendasi*: Ubah nilai default `.env.example` untuk `CACHE_STORE` dari `redis` ke `file` atau `database`. Gunakan Redis hanya ketika mode produksi diaktifkan.
2. **Penggunaan Application Server yang Mendukung Concurrency**:
   - *Rekomendasi*: Untuk pengujian performa local yang lebih baik, jalankan Laravel menggunakan server seperti **Laravel Octane** (dengan RoadRunner/Swoole) atau setup Nginx + PHP-FPM di Laragon, bukan sekadar menggunakan `php artisan serve` yang single-threaded.
3. **Pemisahan API Endpoint**:
   - *Rekomendasi*: Jika dashboard summary dan SAW priority dibutuhkan untuk aplikasi mobile, implementasikan endpoint controller baru di `routes/api.php` agar dapat dipanggil secara terpisah menggunakan format JSON.

---

## H. Kesimpulan
Berdasarkan skenario pengujian yang dilakukan, fitur utama web admin HOMI pada scope tenant Hawaii Garden berhasil dijalankan dengan baik. Data yang tampil berada dalam scope tenant Hawaii Garden dan tidak ditemukan indikasi data tenant lain muncul selama pengujian berlangsung. Namun, pengujian ini masih terbatas pada skenario internal dan belum dapat dianggap sebagai audit keamanan menyeluruh.

Beberapa bottleneck performa lokal dan bug konfigurasi Redis telah didokumentasikan dan diberikan solusi perbaikan untuk kelanjutan pengembangan menuju tahap produksi.
