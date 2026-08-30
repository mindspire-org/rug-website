#!/bin/bash
# ============================================================
#  Hostinger Server Deployment Script
#  Domain: costikyan.mindspire.org
#  Run this AFTER uploading & extracting the ZIP
#  SSH into Hostinger, cd to ~/public_html, then run:
#    bash deploy-on-server.sh
# ============================================================

set -e

echo "========================================"
echo "  Costikyan Deployment Script          "
echo "  Domain: costikyan.mindspire.org      "
echo "========================================"

# 0. Check PHP version and diagnose 403 issues
echo ""
echo "[0/9] PHP Version:"
php -v | head -1

# Quick diagnosis for 403 error
echo ""
echo "Diagnosing 403 error..."
if [ ! -d public ]; then
    echo "Creating public/ folder..."
    mkdir -p public
fi

# Move files if they're in wrong location
if [ -f index.php ] && [ ! -f public/index.php ]; then
    echo "Moving files to public/ folder..."
    mv index.php public/
    mv .htaccess public/ 2>/dev/null || true
fi

# Ensure critical files exist
if [ ! -f public/.htaccess ]; then
    echo "Creating .htaccess..."
    cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
fi

# 1. Install PHP dependencies
echo ""
echo "[1/9] Installing PHP dependencies..."
if [ -f ~/composer.phar ]; then
    php ~/composer.phar install --no-dev --optimize-autoloader
elif command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader
else
    echo "ERROR: composer not found. Installing..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=~ --filename=composer.phar
    rm composer-setup.php
    php ~/composer.phar install --no-dev --optimize-autoloader
fi

# 2. Set up .env for production
echo ""
echo "[2/9] Setting up .env file..."
if [ ! -f .env ]; then
    # Check if we have the pre-configured .env.hostinger file
    if [ -f .env.hostinger ]; then
        cp .env.hostinger .env
        echo "Using pre-configured .env with real Hostinger credentials"
    else
        cp env-production-template.txt .env
        # Set the domain and database credentials
        sed -i 's|https://yourdomain.com|https://costikyan.mindspire.org|g' .env
        sed -i 's|YOUR_HOSTINGER_MYSQL_HOST|localhost|g' .env
        sed -i 's|YOUR_HOSTINGER_DB_NAME|u714104226_rug|g' .env
        sed -i 's|YOUR_HOSTINGER_DB_USER|u714104226_rug|g' .env
        sed -i 's|YOUR_HOSTINGER_DB_PASSWORD|Qut@!b@h5566@|g' .env
        echo "Created .env with Hostinger MySQL credentials"
    fi
    echo ""
    echo "NOTE: Only Stripe keys need to be added manually"
    echo "To edit: nano .env"
    echo "Add your live Stripe keys:"
    echo "  STRIPE_KEY=pk_live_..."
    echo "  STRIPE_SECRET=sk_live_..."
    echo ""
fi

# 3. Generate app key
echo ""
echo "[3/9] Generating app key..."
php artisan key:generate --force

# 4. Create storage symlink
echo ""
echo "[4/9] Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || echo "Symlink may already exist"

# 5. Set permissions
echo ""
echo "[5/9] Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env

# 6. Run migrations
echo ""
echo "[6/9] Running database migrations..."
php artisan migrate --force

# 7. Seed database
echo ""
echo "[7/9] Seeding database..."
php artisan db:seed --force 2>/dev/null || echo "Seeders may have already run"

# 8. Optimize
echo ""
echo "[8/9] Optimizing application..."
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Final status
echo ""
echo "[9/9] Checking application..."
php artisan about | head -20

echo ""
echo "========================================"
echo "  DEPLOYMENT COMPLETE                  "
echo "========================================"
echo ""
echo "Your site should be live at:"
echo "  https://costikyan.mindspire.org"
echo ""
echo "Admin panel: https://costikyan.mindspire.org/admin"
echo "Default admin: admin@example.com / password"
echo ""
echo "If you see errors, check:"
echo "  tail -n 50 storage/logs/laravel.log"
echo ""
