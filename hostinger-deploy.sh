#!/bin/bash
# Hostinger Deployment Script for Costikyan Custom Carpet
# Run this via SSH on your Hostinger server after uploading files

echo "=========================================="
echo "  Costikyan — Hostinger Deploy Script"
echo "=========================================="

# 1. Navigate to project root (adjust if your path differs)
cd ~/public_html || cd ~/domains/costikyan.mindspire.org/public_html || exit 1

echo ""
echo "[1/8] Installing Composer dependencies (no-dev)..."
php ~/composer.phar install --no-dev --optimize-autoloader --no-interaction

echo ""
echo "[2/8] Generating APP_KEY (if missing)..."
php artisan key:generate --no-interaction 2>/dev/null || echo "Key already set"

echo ""
echo "[3/8] Running database migrations..."
php artisan migrate --force --no-interaction

echo ""
echo "[4/8] Seeding database (safe: uses firstOrCreate)..."
php artisan db:seed --class=CostikyanSeeder --force --no-interaction

echo ""
echo "[5/8] Caching config, routes, and views (CRITICAL for speed)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ""
echo "[6/8] Fixing storage permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/storage 2>/dev/null || true

echo ""
echo "[7/8] Deleting broken storage symlink & creating real directory..."
# On Hostinger, the symlink often breaks — replace with real directory
if [ -L public/storage ]; then
    rm public/storage
    mkdir -p public/storage/products
    echo "    Replaced symlink with real directory"
fi
# Copy existing images from storage/app/public to public/storage
if [ -d storage/app/public/products ]; then
    cp -r storage/app/public/products/* public/storage/products/ 2>/dev/null || true
    echo "    Copied images to public/storage/"
fi

echo ""
echo "[8/8] Clearing old log bloat..."
php artisan log:clear 2>/dev/null || true

echo ""
echo "=========================================="
echo "  ✅ DEPLOYMENT COMPLETE"
echo "=========================================="
echo ""
echo "NEXT STEPS:"
echo "  1. Go to https://uptimerobot.com (free plan)"
echo "  2. Add monitor: https://costikyan.mindspire.org/health"
echo "  3. Set interval to 5 minutes"
echo "  4. This keeps PHP-FPM alive and prevents the 10-min downtime"
echo ""
echo "If images still break after upload:"
echo "  - Ensure public/storage/products/ is a REAL directory, not symlink"
echo "  - Check that uploaded files exist in both:"
echo "      storage/app/public/products/"
echo "      public/storage/products/"
echo ""
