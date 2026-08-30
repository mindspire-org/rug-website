#!/bin/bash
# ============================================================
#  Quick Fix for 403 Error on Hostinger
#  Run this if you're getting 403 Forbidden
# ============================================================

echo "========================================"
echo "  Quick 403 Error Fix                   "
echo "========================================"

cd ~/public_html

echo ""
echo "Current directory: $(pwd)"
echo "Checking file structure..."

# Check if files are in wrong location
if [ -f index.php ] && [ ! -d public ]; then
    echo "Creating public/ folder and moving files..."
    mkdir -p public
    mv index.php public/
    mv .htaccess public/ 2>/dev/null || true
    cp -r css public/ 2>/dev/null || true
    cp -r js public/ 2>/dev/null || true
    cp -r images public/ 2>/dev/null || true
    cp -r build public/ 2>/dev/null || true
fi

# Create .htaccess if missing
if [ ! -f public/.htaccess ]; then
    echo "Creating .htaccess in public/..."
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

# Set permissions
echo "Setting permissions..."
chmod 644 public/.htaccess
chmod 644 public/index.php
chmod -R 755 storage 2>/dev/null || true
chmod -R 755 bootstrap/cache 2>/dev/null || true

echo ""
echo "Final check:"
echo "public/index.php: $([ -f public/index.php ] && echo '✓ EXISTS' || echo '✗ MISSING')"
echo "public/.htaccess: $([ -f public/.htaccess ] && echo '✓ EXISTS' || echo '✗ MISSING')"
echo "public/build folder: $([ -d public/build ] && echo '✓ EXISTS' || echo '✗ MISSING')"

echo ""
echo "========================================"
echo "  Fix Complete                          "
echo "========================================"
echo ""
echo "NEXT STEPS:"
echo "1. In Hostinger hPanel, set Document Root to: public_html/public"
echo "2. Wait 1-2 minutes for changes to take effect"
echo "3. Test: https://costikyan.mindspire.org"
echo ""
echo "If still 403, run: bash diagnose-server.sh"
