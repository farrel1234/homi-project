#!/bin/bash
# =============================================================
# Homi Backend — VPS Deployment Script
# Jalankan script ini di VPS kamu (Ubuntu 20.04/22.04)
# =============================================================

set -e  # Exit on error

# ─── KONFIGURASI — GANTI SESUAI VPS KAMU ─────────────────────
DOMAIN="api.homi.id"         # Subdomain API backend
APP_PORT="8001"               # Port internal Docker
APP_DIR="/var/www/homi"       # Direktori project di VPS
GITHUB_REPO="https://github.com/USERNAME/homi-backend.git"  # GANTI!
BRANCH="main"
# ──────────────────────────────────────────────────────────────

echo "======================================================"
echo " 🚀 Homi Backend — VPS Setup & Deploy"
echo "======================================================"

# ─── 1. Update sistem ─────────────────────────────────────────
echo ""
echo "[1/8] Updating system packages..."
sudo apt-get update -y && sudo apt-get upgrade -y

# ─── 2. Install Docker ────────────────────────────────────────
echo ""
echo "[2/8] Installing Docker..."
if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    rm get-docker.sh
    echo "✅ Docker installed."
else
    echo "✅ Docker already installed."
fi

# ─── 3. Install Docker Compose ────────────────────────────────
echo ""
echo "[3/8] Installing Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    echo "✅ Docker Compose installed."
else
    echo "✅ Docker Compose already installed."
fi

# ─── 4. Install Nginx & Certbot ───────────────────────────────
echo ""
echo "[4/8] Installing Nginx & Certbot..."
sudo apt-get install -y nginx certbot python3-certbot-nginx

# ─── 5. Clone / Pull project ──────────────────────────────────
echo ""
echo "[5/8] Setting up project..."
if [ -d "$APP_DIR" ]; then
    echo "📥 Pulling latest code..."
    cd $APP_DIR
    git pull origin $BRANCH
else
    echo "📥 Cloning repository..."
    sudo mkdir -p $APP_DIR
    sudo chown $USER:$USER $APP_DIR
    git clone $GITHUB_REPO $APP_DIR
    cd $APP_DIR
fi

# ─── 6. Setup .env ────────────────────────────────────────────
echo ""
echo "[6/8] Setting up environment..."
if [ ! -f "$APP_DIR/.env" ]; then
    cp $APP_DIR/.env.production $APP_DIR/.env
    echo "⚠️  File .env dibuat dari .env.production"
    echo "⚠️  HARAP ISI NILAI YANG KOSONG DI .env SEBELUM LANJUT!"
    echo "    nano $APP_DIR/.env"
    read -p "Tekan Enter setelah selesai mengisi .env..."
fi

# ─── 7. Build & Run Docker ────────────────────────────────────
echo ""
echo "[7/8] Building and starting Docker containers..."
cd $APP_DIR
docker-compose up -d --build

# Tunggu MySQL siap
echo "⏳ Waiting for database to be ready..."
sleep 15

# Run migrations
echo "🔄 Running migrations..."
docker exec homi-app php artisan migrate --force
docker exec homi-app php artisan storage:link
docker exec homi-app php artisan config:cache
docker exec homi-app php artisan route:cache
docker exec homi-app php artisan view:cache

# Buat folder downloads untuk APK
docker exec homi-app mkdir -p public/downloads

echo "✅ Docker containers running!"
docker ps | grep homi

# ─── 8. Setup Nginx Reverse Proxy ─────────────────────────────
echo ""
echo "[8/8] Setting up Nginx reverse proxy..."

sudo tee /etc/nginx/sites-available/homi-api > /dev/null <<EOF
server {
    listen 80;
    server_name ${DOMAIN};

    # Redirect semua HTTP ke HTTPS (aktif setelah certbot)
    location / {
        proxy_pass http://127.0.0.1:${APP_PORT};
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_read_timeout 300;
        client_max_body_size 20M;
    }
}
EOF

sudo ln -sf /etc/nginx/sites-available/homi-api /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# ─── SSL dengan Let's Encrypt ─────────────────────────────────
echo ""
echo "🔒 Setting up SSL (Let's Encrypt)..."
echo "Pastikan domain $DOMAIN sudah diarahkan ke IP VPS ini!"
read -p "Domain sudah diarahkan? (y/n): " dns_ready

if [ "$dns_ready" = "y" ]; then
    sudo certbot --nginx -d $DOMAIN --non-interactive --agree-tos -m admin@homi.id
    echo "✅ SSL configured!"
else
    echo "⚠️  Skip SSL. Jalankan manual: sudo certbot --nginx -d $DOMAIN"
fi

# ─── Selesai ──────────────────────────────────────────────────
echo ""
echo "======================================================"
echo " ✅ DEPLOYMENT SELESAI!"
echo "======================================================"
echo ""
echo " 🌐 API URL   : https://${DOMAIN}"
echo " 📱 Download  : https://${DOMAIN}/download"
echo " 🗄️  DB Port  : 33066 (internal)"
echo ""
echo " Perintah berguna:"
echo "   docker ps                          - lihat status container"
echo "   docker logs homi-app -f            - log app"
echo "   docker logs homi-queue -f          - log queue worker"
echo "   docker exec homi-app php artisan   - jalankan artisan"
echo ""
