# PowerShell script to setup PHP and run Laravel
# Run as: powershell -ExecutionPolicy Bypass -File setup-php.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  PHP Setup & Laravel Server Starter" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Find PHP zip file
$phpZipPatterns = @(
    "$env:USERPROFILE\Downloads\php*.zip",
    "$env:USERPROFILE\Downloads\php-8.*.zip",
    "C:\Users\*\Downloads\php*.zip"
)

$phpZip = $null
foreach ($pattern in $phpZipPatterns) {
    $found = Get-ChildItem -Path $pattern -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($found) {
        $phpZip = $found.FullName
        break
    }
}

if (-not $phpZip) {
    Write-Host "❌ PHP zip file not found in Downloads" -ForegroundColor Red
    Write-Host "Please download PHP from: https://windows.php.net/download/" -ForegroundColor Yellow
    exit 1
}

Write-Host "📦 Found PHP archive: $phpZip" -ForegroundColor Green

# Extract PHP if not already extracted
if (-not (Test-Path "C:\php\php.exe")) {
    Write-Host "📂 Extracting PHP to C:\php..." -ForegroundColor Yellow
    try {
        Expand-Archive -Path $phpZip -DestinationPath "C:\php" -Force
        Write-Host "✅ PHP extracted successfully!" -ForegroundColor Green
    } catch {
        Write-Host "❌ Failed to extract: $_" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "✅ PHP already installed at C:\php" -ForegroundColor Green
}

# Add to PATH for this session
$env:Path += ";C:\php"

# Verify PHP
Write-Host ""
Write-Host "🔍 Verifying PHP installation..." -ForegroundColor Cyan
$phpVersion = php -v 2>&1 | Select-Object -First 1
if ($phpVersion -match "PHP") {
    Write-Host "✅ $phpVersion" -ForegroundColor Green
} else {
    Write-Host "❌ PHP verification failed" -ForegroundColor Red
    exit 1
}

# Check for vendor directory
$vendorPath = "E:\prime-smile-lab-67-main\github\rug-website\vendor"
if (-not (Test-Path "$vendorPath\autoload.php")) {
    Write-Host ""
    Write-Host "⚠️  Vendor directory missing or incomplete!" -ForegroundColor Yellow
    Write-Host "📦 Extracting vendor.zip..." -ForegroundColor Yellow
    
    $vendorZip = "E:\prime-smile-lab-67-main\github\rug-website\vendor.zip"
    if (Test-Path $vendorZip) {
        Expand-Archive -Path $vendorZip -DestinationPath "$vendorPath-extract" -Force
        if (Test-Path "$vendorPath-extract\vendor") {
            Copy-Item -Path "$vendorPath-extract\vendor\*" -Destination $vendorPath -Recurse -Force
            Remove-Item "$vendorPath-extract" -Recurse -Force
            Write-Host "✅ Vendor extracted!" -ForegroundColor Green
        }
    } else {
        Write-Host "❌ vendor.zip not found!" -ForegroundColor Red
        exit 1
    }
}

# Change to project directory
Set-Location "E:\prime-smile-lab-67-main\github\rug-website"

Write-Host ""
Write-Host "🚀 Starting Laravel development server..." -ForegroundColor Green
Write-Host "📱 Your app will be at: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "🔧 Admin panel: http://127.0.0.1:8000/admin" -ForegroundColor Cyan
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host "========================================" -ForegroundColor Cyan

# Start Laravel server
php artisan serve --host=127.0.0.1 --port=8000
