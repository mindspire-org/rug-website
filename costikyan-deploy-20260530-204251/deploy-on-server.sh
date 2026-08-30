#!/bin/bash
# ============================================================
#  Hostinger Server Deployment Script
#  Run this AFTER uploading files via File Manager or FTP
#  SSH into Hostinger, cd to your project folder, then run:
#    bash deploy-on-server.sh
# ============================================================

set -e

echo "========================================"
echo "  Costikyan Deployment Script          "
echo "========================================"

# 1. Install PHP dependencies
echo ""
echo "[1/8] Installing PHP dependencies..."
php ~/composer.phar install --no-dev --optimize-autoloader 2>/dev/null || composer install --no-dev --optimize-autoloader

# 2. Copy production env
echo ""
echo "[2/8] Setting up .env file..."
if [ ! -f .env ]; then
    cp env-production-template.txt .env
    echo "WARNING: .env created from template. You MUST edit it with your real credentials!"
fi

# 3. Generate app key
echo ""
echo "[3/8] Generating app key..."
php artisan key:generate --force

# 4. Create storage symlink
echo ""
echo "[4/8] Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || echo "Symlink may already exist"

# 5. Set permissions
echo ""
echo "[5/8] Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env

# 6. Run migrations
echo ""
echo "[6/8] Running database migrations..."
php artisan migrate --force

# 7. Seed database (optional - creates admin user, categories, etc.)
echo ""
echo "[7/8] Seeding database..."
php artisan db:seed --force 2>/dev/null || echo "Seeders may have already run"

# 8. Optimize
echo ""
echo "[8/8] Optimizing application..."
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "========================================"
echo "  DEPLOYMENT COMPLETE                  "
echo "========================================"
echo ""
echo "IMPORTANT: If this is your FIRST deploy,"
echo "edit .env and replace ALL placeholder values:"
echo "  - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
echo "  - APP_URL (your domain)"
echo "  - STRIPE_KEY, STRIPE_SECRET, STRIPE_WEBHOOK_SECRET"
echo "  - MAIL_* settings"
echo ""
echo "After editing .env, run: php artisan config:cache"
echo ""
echo "Your site should be live at: https://yourdomain.com"
