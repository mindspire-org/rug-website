# ============================================================
#  Costikyan Custom Carpet — Hostinger Build Script
#  Run this from your project root to create a production ZIP
# ============================================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Building for Hostinger Deployment     " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# 1. Install production PHP dependencies
Write-Host "`n[1/6] Installing PHP dependencies (no dev)..." -ForegroundColor Yellow
composer install --no-dev --optimize-autoloader
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: composer install failed" -ForegroundColor Red
    exit 1
}
Write-Host "Done." -ForegroundColor Green

# 2. Build frontend assets
Write-Host "`n[2/6] Building frontend assets..." -ForegroundColor Yellow
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: npm build failed" -ForegroundColor Red
    exit 1
}
Write-Host "Done." -ForegroundColor Green

# 3. Clear local caches
Write-Host "`n[3/6] Clearing local caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
Write-Host "Done." -ForegroundColor Green

# 4. Generate production .env file
Write-Host "`n[4/6] Creating production .env..." -ForegroundColor Yellow
if (Test-Path .env.production) {
    Copy-Item .env.production .env -Force
    Write-Host "Copied .env.production -> .env" -ForegroundColor Green
} else {
    Write-Host "WARNING: .env.production not found. Using .env.example as base." -ForegroundColor Yellow
    Copy-Item .env.example .env -Force
}
php artisan key:generate --force
Write-Host "App key generated." -ForegroundColor Green

# 5. Create deployment ZIP
Write-Host "`n[5/6] Creating deployment ZIP..." -ForegroundColor Yellow

$exclude = @(
    "node_modules", ".git", "tests", 
    "*.log", "*.sqlite", "*.sqlite3",
    ".env.local", ".env.development",
    "build-for-hostinger.ps1",
    "DEPLOYMENT.md",
    "env-production-template.txt"
)

$zipName = "costikyan-deploy-$(Get-Date -Format 'yyyyMMdd-HHmmss').zip"

Compress-Archive -Path @(
    "app", "bootstrap", "config", "database",
    "public", "resources", "routes", "storage",
    "vendor", "artisan", "composer.json",
    "composer.lock", "package.json", ".env"
) -DestinationPath $zipName -Force

Write-Host "Done." -ForegroundColor Green

# 6. Summary
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  BUILD COMPLETE                         " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Output file: $zipName" -ForegroundColor White
Write-Host "Size: $([math]::Round((Get-Item $zipName).Length / 1MB, 2)) MB" -ForegroundColor White
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "  1. Upload $zipName to Hostinger public_html/"
Write-Host "  2. Extract on server"
Write-Host "  3. Set document root to public_html/public/"
Write-Host "  4. Copy env-production-template.txt contents into .env"
Write-Host "  5. Edit .env with your Hostinger DB credentials"
Write-Host "  6. Run: php artisan migrate --force && php artisan optimize"
Write-Host "`nFor full instructions, see DEPLOYMENT.md" -ForegroundColor Cyan
