#!/bin/bash
# =============================================================
# Homi — Update deployment (jalankan saat ada update code)
# Gunakan script ini setelah push ke GitHub
# =============================================================

APP_DIR="/var/www/homi"

echo "🔄 Pulling latest code..."
cd $APP_DIR
git pull origin main

echo "🔄 Rebuilding containers if needed..."
docker-compose up -d --build

echo "🔄 Running migrations..."
docker exec homi-app php artisan migrate --force

echo "🧹 Clearing caches..."
docker exec homi-app php artisan config:cache
docker exec homi-app php artisan route:cache
docker exec homi-app php artisan view:cache

echo "✅ Update selesai!"
docker ps | grep homi
