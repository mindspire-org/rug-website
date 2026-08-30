#!/bin/bash
# ============================================================
#  Hostinger Server Diagnosis Script
#  Run this to check file structure and fix 403 errors
# ============================================================

echo "========================================"
echo "  Server Diagnosis for 403 Error       "
echo "========================================"

echo ""
echo "[1] Current directory structure:"
pwd
echo ""
echo "Contents of ~/public_html/:"
ls -la ~/public_html/
echo ""
if [ -d ~/public_html/public ]; then
    echo "Contents of ~/public_html/public/:"
    ls -la ~/public_html/public/
else
    echo "ERROR: ~/public_html/public/ does NOT exist!"
fi

echo ""
echo "[2] Checking for critical files:"
echo "index.php in public_html/public/:"
if [ -f ~/public_html/public/index.php ]; then
    echo "  ✓ EXISTS"
    echo "  Size: $(stat -c%s ~/public_html/public/index.php) bytes"
else
    echo "  ✗ MISSING"
fi

echo ".htaccess in public_html/public/:"
if [ -f ~/public_html/public/.htaccess ]; then
    echo "  ✓ EXISTS"
    echo "  Size: $(stat -c%s ~/public_html/public/.htaccess) bytes"
else
    echo "  ✗ MISSING"
fi

echo ""
echo "[3] Checking Laravel structure:"
echo "app/ folder: $([ -d ~/public_html/app ] && echo '✓' || echo '✗')"
echo "vendor/ folder: $([ -d ~/public_html/vendor ] && echo '✓' || echo '✗')"
echo ".env file: $([ -f ~/public_html/.env ] && echo '✓' || echo '✗')"
echo "artisan file: $([ -f ~/public_html/artisan ] && echo '✓' || echo '✗')"

echo ""
echo "[4] Fixing common issues..."

# Fix 1: Create public folder if missing
if [ ! -d ~/public_html/public ]; then
    echo "Creating public/ folder..."
    mkdir -p ~/public_html/public
fi

# Fix 2: Move files if they're in wrong location
if [ -f ~/public_html/index.php ] && [ ! -f ~/public_html/public/index.php ]; then
    echo "Moving files from public_html/ to public_html/public/..."
    mv ~/public_html/index.php ~/public_html/public/
    mv ~/public_html/.htaccess ~/public_html/public/ 2>/dev/null || true
    cp -r ~/public_html/css ~/public_html/public/ 2>/dev/null || true
    cp -r ~/public_html/js ~/public_html/public/ 2>/dev/null || true
    cp -r ~/public_html/images ~/public_html/public/ 2>/dev/null || true
fi

# Fix 3: Create basic .htaccess if missing
if [ ! -f ~/public_html/public/.htaccess ]; then
    echo "Creating basic .htaccess..."
    cat > ~/public_html/public/.htaccess << 'EOF'
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

# Fix 4: Set permissions
echo "Setting permissions..."
chmod -R 755 ~/public_html/storage 2>/dev/null || true
chmod -R 755 ~/public_html/bootstrap/cache 2>/dev/null || true
chmod 644 ~/public_html/.env 2>/dev/null || true
chmod 644 ~/public_html/public/.htaccess 2>/dev/null || true
chmod 644 ~/public_html/public/index.php 2>/dev/null || true

echo ""
echo "[5] Final check:"
echo "index.php in public_html/public/: $([ -f ~/public_html/public/index.php ] && echo '✓' || echo '✗')"
echo ".htaccess in public_html/public/: $([ -f ~/public_html/public/.htaccess ] && echo '✓' || echo '✗')"
echo "Document root should be: public_html/public"

echo ""
echo "========================================"
echo "  Diagnosis Complete                    "
echo "========================================"
echo ""
echo "If files are now in place, update document root in hPanel:"
echo "  Websites → Manage → Advanced → Document Root"
echo "  Change to: public_html/public"
echo ""
echo "Then test: https://costikyan.mindspire.org"
