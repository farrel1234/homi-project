# Multi-Tenant (Database Per Perumahan)

## Konsep
- `central` DB: simpan daftar tenant/perumahan (`tenants` table).
- `tenant` DB: data operasional masing-masing perumahan (users, complaints, fees, dll).

## Resolver Tenant
API akan mencari tenant code dari:
1. Header `X-Tenant-Code`
2. Body/query key: `tenant_code`, `tenant`, atau `housing_code`
3. Mapping host/domain ke kolom `tenants.domain` (opsional)

Jika tenant tidak ditemukan, API return error.

## Setup Cepat
1. Atur `.env`:
   - `CENTRAL_DB_*` untuk DB pusat
   - `TENANCY_REQUIRED=true`
2. Jalankan migrasi central:
   - `php artisan migrate`
3. Isi tabel `tenants` dengan data perumahan + kredensial DB tenant.
4. Kirim `X-Tenant-Code` di setiap request API mobile/web client.

## Artisan Helper
- Buat/update tenant cepat:
  - `php artisan tenant:upsert hawaii-garden "Hawaii Garden" homi_backend`
- Contoh tenant baru:
  - `php artisan tenant:upsert green-lake "Green Lake Residence" homi_greenlake --host=127.0.0.1 --port=3306 --username=root --password=`

## Catatan
- Endpoint health `GET /api/ping` dan debug `GET /api/__debug/php` tidak wajib tenant code.
- Untuk mode transisi, bisa set `TENANCY_FALLBACK_TENANT_CODE` agar client lama tetap jalan.
