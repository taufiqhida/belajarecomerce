#!/bin/bash
# ═══════════════════════════════════════════════════
# deploy.sh — Script deploy ke VPS
# Jalankan: bash deploy.sh
# ═══════════════════════════════════════════════════
set -e

echo "🚀 Starting deployment..."

# ── Pull latest code ────────────────────────────
echo "📥 Pulling latest code from GitHub..."
git pull origin main

# ── Build & restart containers ──────────────────
echo "🐳 Building Docker images..."
docker compose build --no-cache app

echo "🔄 Restarting services..."
docker compose up -d --force-recreate app nginx queue scheduler

# ── Wait for DB ─────────────────────────────────
echo "⏳ Waiting for database..."
sleep 5

# ── Run Laravel tasks ───────────────────────────
echo "⚙️  Running artisan commands..."
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan filament:upgrade

# ── Set permissions ─────────────────────────────
echo "🔐 Setting permissions..."
docker compose exec app chown -R www-data:www-data /var/www/html/storage
docker compose exec app chmod -R 775 /var/www/html/storage

echo "✅ Deploy selesai!"
echo "🌐 App berjalan di: $(grep APP_URL .env.production | cut -d= -f2)"
