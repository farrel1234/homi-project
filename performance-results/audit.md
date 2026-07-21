# Audit Project: Platform HOMI Multi-Tenant SaaS

**Tanggal Audit**: 2026-07-21  
**Project**: Platform HOMI (Backend Laravel Multi-Tenant)  
**Tujuan Audit**: Persiapan Pengujian Latensi API AAS Kapita Selekta  

---

## 1. Environment & Stack Version
* **PHP Version**: PHP 8.3.30 (cli) (ZTS Visual C++ 2019 x64)
* **Laravel Version**: 12.40.2
* **Database Engine**: MySQL 8.4.3 (Windows x64, datadir: `C:\laragon\data\mysql-8.4`)
* **Redis Engine**: Redis 5.0.14.1 (`C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-server.exe`)
* **OS / Host**: Windows 11 64-bit

---

## 2. Alur Tenant Resolution
* **Middleware**: `App\Http\Middleware\ResolveTenant` yang terdaftar pada middleware group `api` di `bootstrap/app.php`.
* **Mekanisme Resolution**:
  1. Memeriksa `payload_keys` (`tenant_code`, `tenant`, `housing_code`).
  2. Memeriksa `header_keys` (`X-Tenant-Code`, `X-Housing-Code`).
  3. Memeriksa `lookup_by_domain` (Host domain matching).
  4. Jika `tenant_code` ditemukan, middleware mengeksekusi query Eloquent ke Landlord/Central Database (`homi`):
     ```php
     $tenant = Tenant::query()
         ->where(function ($q) use ($tenantCodeSearch) {
             $q->where('code', $tenantCodeSearch)
               ->orWhere('registration_code', $tenantCodeSearch);
         })
         ->where('is_active', true)
         ->first();
     ```
  5. Setelah record `Tenant` diperoleh, middleware memanggil `TenantManager::activate($tenant)`.

---

## 3. Proses Dynamic Database Switching
* **File Handling**: `App\Support\Tenancy\TenantManager::activate()` & `App\Services\TenantService::switchToTenant()`.
* **Mekanisme Reset**:
  ```php
  $connectionName = config('database.default', 'mysql');
  config(["database.connections.{$connectionName}.database" => $tenant->db_database]);
  DB::purge($connectionName);
  DB::reconnect($connectionName);
  app()->instance('currentTenant', $tenant);
  ```
* **Keterangan**: Setiap request memutus koneksi MySQL lama (`DB::purge`), mengubah target nama database di config secara runtime, lalu membuka koneksi MySQL baru (`DB::reconnect`).

---

## 4. Status Implementasi Redis Tenant Context Caching
* **Kondisi Audit**: **BELUM DIIMPLEMENTASIKAN** pada jalur `ResolveTenant` murni.
* **Analisis**: Setiap request API tanpa caching akan melakukan 1x query IO disk ke database `homi` (tabel `tenants`) hanya untuk menyelesaikan identitas tenant sebelum query bisnis dijalankan di database tenant (`homi_hawaii_db`).
* **Rencana Implementasi Caching**:
  - Key Format: `tenant:code:{tenant_code}`
  - Cache Driver: `redis`
  - TTL: 3600 detik (1 jam)
  - Config Flag: `TENANT_CACHE_ENABLED=true/false` (atau `config('tenancy.cache_enabled')`).

---

## 5. Status Connection Pooling
* **Kondisi Audit**: **NOT IMPLEMENTED / NOT AVAILABLE** pada environment Windows lokal.
* **Penyebab**:
  - Tidak terdapat ProxySQL, PgBouncer, Laravel Octane (Swoole/RoadRunner), atau persistent database proxy yang berjalan di Windows lokal.
  - Opsi `PDO::ATTR_PERSISTENT` bawaan PHP CLI server (`artisan serve`) tidak mengimplementasikan connection pool sejati (koneksi diputus/direconnect secara sekuensial pada `DB::purge`).
* **Keputusan Audit**:
  - Konfigurasi **POOLING-ONLY** dan **HYBRID** akan ditandai secara jujur sebagai `NOT AVAILABLE (No Local DB Proxy)` sesuai prinsip integritas akademik AAS.
  - Pengujian empiris difokuskan pada perbandingan ilmiah antara **BASELINE (Tenant Cache OFF)** vs **REDIS-ONLY (Tenant Cache ON)**.

---

## 6. Audit Artificial Delays / Code Modifications
* **Pencarian Codebase**: Diperiksa terhadap `sleep()`, `usleep()`, `delay()`, `simulatedDatabaseLookup()`.
* **Hasil**: **0 artificial delay / sleep** ditemukan pada seluruh controller, middleware, dan service backend (`app/`).
* **Kesimpulan**: Aplikasi HOMI berjalan murni tanpa rekayasa latensi buatan.

---

## 7. Endpoint API yang Dipilih untuk Load Testing
* **Endpoint**: `GET /api/announcements`
* **Header Wajib**:
  - `Accept: application/json`
  - `Authorization: Bearer <SANCTUM_TOKEN_TESTING>`
  - `X-Tenant-Code: hawaii-garden`
* **Karakteristik Endpoint**:
  1. **Read-Only**: Hanya melakukan query `SELECT` data pengumuman dari database tenant (`homi_hawaii_db`).
  2. **Selalu Memicu Tenant Resolution**: Setiap request diproses oleh middleware `ResolveTenant`.
  3. **Response Konsisten**: Mengembalikan HTTP 200 OK dengan payload JSON valid `{ "status": true, "data": [...] }`.
  4. **Aman untuk Concurrency High-Load Testing**: Tidak mengubah state database maupun file storage.

---

## 8. Status Service & Benchmark Tools
* **MySQL 8.4**: Running (Port 3306)
* **Redis 5.0**: Binary tersedia di `C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-server.exe` (Dapat diaktifkan pada port 6379).
* **Load Testing Tool**: `k6 v2.0.0-rc1` (Tersedia dan berfungsi pada PATH Windows).
