#!/bin/bash
# Deployment script for costikyancustomcarpet.com on Hostinger
# Run via SSH after the archive is uploaded
set -e

DOMAIN_DIR="$HOME/domains/costikyancustomcarpet.com"
ARCHIVE="$HOME/costikyan-deploy.tar.gz"

echo "=========================================="
echo "  Deploying Costikyan -> costikyancustomcarpet.com"
echo "=========================================="

# 1. Back up existing default page, clean public_html
echo "[1/9] Preparing target directory..."
cd "$DOMAIN_DIR"
# Move existing default.php aside
if [ -f public_html/default.php ] && [ ! -f public_html/index.php ]; then
    mv public_html/default.php public_html/default.php.bak 2>/dev/null || true
fi

# 2. Extract archive into a temp dir, then move into public_html
echo "[2/9] Extracting archive..."
TMPDIR="$DOMAIN_DIR/.deploy_tmp"
rm -rf "$TMPDIR"
mkdir -p "$TMPDIR"
tar -xzf "$ARCHIVE" -C "$TMPDIR"

# 3. Move extracted contents into public_html (Laravel root lives here)
echo "[3/9] Moving files into public_html..."
# Clear public_html of old content (keep nothing from default)
find public_html -mindepth 1 -delete 2>/dev/null || true
# Move all extracted top-level items into public_html
shopt -s dotglob
mv "$TMPDIR"/* public_html/
shopt -u dotglob
rmdir "$TMPDIR" 2>/dev/null || true

cd public_html

# 4. Create .env (will be written separately before this step)
echo "[4/9] Ensuring .env exists..."
if [ ! -f .env ]; then
    echo "ERROR: .env not found. Please create it before running migrations."
    exit 1
fi

# 5. Composer (vendor already uploaded; just optimize autoloader)
echo "[5/9] Optimizing composer autoloader..."
composer dump-autoload --optimize --no-dev 2>/dev/null || \
    php ~/composer.phar dump-autoload --optimize --no-dev 2>/dev/null || true

# 6. Generate key if missing, then migrate + seed
echo "[6/9] Running migrations and seeders..."
php artisan key:generate --no-interaction 2>/dev/null || true
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

# 7. Cache config, routes, views, events
echo "[7/9] Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true

# 8. Permissions + storage link
echo "[8/9] Setting permissions and storage link..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
# Replace symlink with real directory (Hostinger symlinks often break)
if [ -L public/storage ]; then
    rm public/storage
fi
mkdir -p public/storage
php artisan storage:link 2>/dev/null || true
# Copy any product images into public/storage
if [ -d storage/app/public/products ]; then
    cp -r storage/app/public/products/* public/storage/products/ 2>/dev/null || true
fi

# 9. Clear logs
echo "[9/9] Clearing old logs..."
php artisan log:clear 2>/dev/null || true

echo ""
echo "=========================================="
echo "  DEPLOYMENT COMPLETE"
echo "=========================================="
echo "  URL: https://costikyancustomcarpet.com"
echo "  Admin: admin@costikyan.com / password"
echo "=========================================="
