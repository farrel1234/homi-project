# 🚀 Homi Backend — Panduan Deploy ke VPS

## Prasyarat

Sebelum mulai, siapkan:
- ✅ VPS Ubuntu 20.04 / 22.04 (min. 2GB RAM)
- ✅ Akses SSH ke VPS
- ✅ Domain/subdomain yang sudah diarahkan ke IP VPS
- ✅ Kode sudah di-push ke GitHub

---

## Arsitektur di VPS

```
Internet (Port 80/443)
        ↓
  [Nginx — Reverse Proxy]  ← di-install langsung di VPS host
        ↓
  ┌─────────────────────────────────────────────┐
  │           Docker Compose Homi               │
  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
  │  │ homi-app │  │ homi-web │  │ homi-db  │  │
  │  │ PHP-FPM  │  │  Nginx   │  │ MySQL    │  │
  │  │  :9000   │  │  :8001   │  │ :33066   │  │
  │  └──────────┘  └──────────┘  └──────────┘  │
  │                                             │
  │  ┌──────────┐                               │
  │  │ homi-    │                               │
  │  │ queue    │  (Queue Worker)               │
  │  └──────────┘                               │
  └─────────────────────────────────────────────┘
```

---

## Step 1 — Siapkan Domain

Di panel domain kamu (Cloudflare, Niagahoster, dll.), tambahkan:

```
Type: A
Name: api          (untuk api.homi.id)
Value: [IP_VPS_KAMU]
TTL: Auto
```

> Tunggu 5–15 menit sampai DNS menyebar.

---

## Step 2 — Push Kode ke GitHub

Di laptop kamu:
```bash
git add .
git commit -m "chore: prepare for VPS deployment"
git push origin main
```

---

## Step 3 — Setup VPS (Otomatis)

SSH ke VPS, lalu jalankan deploy script:

```bash
ssh root@[IP_VPS_KAMU]

# Download & jalankan script
wget https://raw.githubusercontent.com/USERNAME/homi-backend/main/deploy.sh
chmod +x deploy.sh
./deploy.sh
```

> Script akan otomatis install Docker, Nginx, clone repo, setup .env, dan SSL.

### Atau Manual Step by Step:

```bash
# 1. Install Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER

# 2. Install Nginx
sudo apt install nginx certbot python3-certbot-nginx -y

# 3. Clone repo
git clone https://github.com/USERNAME/homi-backend.git /var/www/homi
cd /var/www/homi

# 4. Setup .env
cp .env.production .env
nano .env   # Isi semua nilai yang kosong [GANTI_INI]

# 5. Build & jalankan Docker
docker-compose up -d --build

# 6. Setup database
docker exec homi-app php artisan key:generate
docker exec homi-app php artisan migrate --force
docker exec homi-app php artisan storage:link
docker exec homi-app php artisan config:cache

# 7. Buat folder download APK
docker exec homi-app mkdir -p public/downloads
```

---

## Step 4 — Konfigurasi .env di VPS

Edit file `.env` di VPS:
```bash
nano /var/www/homi/.env
```

Pastikan nilai ini benar:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.homi.id

# DB Host harus nama container Docker!
DB_HOST=db
CENTRAL_DB_HOST=db
```

---

## Step 5 — Setup Nginx Reverse Proxy

```bash
sudo nano /etc/nginx/sites-available/homi-api
```

Isi dengan:
```nginx
server {
    listen 80;
    server_name api.homi.id;

    location / {
        proxy_pass http://127.0.0.1:8001;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 300;
        client_max_body_size 20M;
    }
}
```

```bash
# Aktifkan & reload
sudo ln -s /etc/nginx/sites-available/homi-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# Install SSL gratis
sudo certbot --nginx -d api.homi.id
```

---

## Step 6 — Upload APK

Setelah build APK dari Android Studio:

```bash
# Upload APK ke VPS
scp homi-app-release.apk root@[IP_VPS]:/var/www/homi/public/downloads/homi-app.apk
```

Atau dari dalam VPS:
```bash
docker exec homi-app ls public/downloads/
```

Setelah upload, halaman download bisa diakses di:
```
https://api.homi.id/download
```

---

## Step 7 — Setup Multi-Project (4 Project)

Untuk setiap project tambahan, gunakan port yang berbeda:

| Project | Port | Subdomain |
|---|---|---|
| Homi Backend | 8001 | api.homi.id |
| Project 2 | 8002 | project2.domain.id |
| Project 3 | 8003 | project3.domain.id |
| Project 4 | 8004 | project4.domain.id |

Buat Nginx config baru untuk setiap project:
```bash
sudo nano /etc/nginx/sites-available/project2
# Ganti server_name dan proxy_pass port
```

---

## Perintah Berguna

```bash
# Lihat status container
docker ps

# Lihat log app
docker logs homi-app -f

# Lihat log queue worker
docker logs homi-queue -f

# Restart semua container
docker-compose restart

# Jalankan artisan command
docker exec homi-app php artisan [command]

# Update setelah push ke GitHub
./update.sh

# Masuk ke dalam container
docker exec -it homi-app bash
```

---

## Troubleshooting

### Container crash / tidak mau start
```bash
docker logs homi-app
# Biasanya karena .env salah atau DB belum siap
```

### 502 Bad Gateway
```bash
# Cek apakah container jalan
docker ps
# Cek port yang dipakai
curl localhost:8001
```

### Permission error di storage
```bash
docker exec homi-app chmod -R 775 storage bootstrap/cache
docker exec homi-app chown -R www-data:www-data storage bootstrap/cache
```

### APK download tidak muncul
- Pastikan file ada: `ls /var/www/homi/public/downloads/homi-app.apk`
- Cek nama file harus exactly `homi-app.apk`
