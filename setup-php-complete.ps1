# Complete PHP Setup for Laravel
Write-Host "Setting up PHP for Laravel..." -ForegroundColor Cyan

# Step 1: Find php.ini
$iniFile = "C:\php\php.ini"
if (-not (Test-Path $iniFile)) {
    if (Test-Path "C:\php\php.ini-development") {
        Copy-Item "C:\php\php.ini-development" $iniFile
        Write-Host "Created php.ini from development template" -ForegroundColor Green
    } else {
        Write-Host "php.ini-development not found!" -ForegroundColor Red
        exit 1
    }
}

# Step 2: Read current ini
$iniContent = Get-Content $iniFile -Raw

# Step 3: Enable all required extensions
$extensions = @(
    'extension=openssl',
    'extension=pdo_sqlite',
    'extension=sqlite3',
    'extension=mbstring',
    'extension=curl',
    'extension=fileinfo',
    'extension=gd',
    'extension=intl'
)

foreach ($ext in $extensions) {
    $extName = $ext -replace 'extension=', ''
    if ($iniContent -match ";extension=$extName") {
        $iniContent = $iniContent -replace ";extension=$extName", $ext
        Write-Host "Enabled: $extName" -ForegroundColor Green
    } elseif ($iniContent -match "extension=$extName") {
        Write-Host "Already enabled: $extName" -ForegroundColor Yellow
    } else {
        # Add extension if not present
        $iniContent += "`n$ext"
        Write-Host "Added: $extName" -ForegroundColor Cyan
    }
}

# Step 4: Set extension directory
if ($iniContent -notmatch "extension_dir") {
    $iniContent = "extension_dir = `"C:\php\ext`"`n" + $iniContent
    Write-Host "Set extension_dir" -ForegroundColor Green
}

# Step 5: Save ini
Set-Content $iniFile $iniContent -NoNewline
Write-Host "`nSaved php.ini" -ForegroundColor Green

# Step 6: Verify
Write-Host "`nVerifying PHP extensions..." -ForegroundColor Cyan
$extensionsOutput = php -m
$required = @('openssl', 'pdo_sqlite', 'sqlite3', 'mbstring', 'curl', 'fileinfo')
foreach ($ext in $required) {
    if ($extensionsOutput -match $ext) {
        Write-Host "  ✅ $ext" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $ext MISSING!" -ForegroundColor Red
    }
}

Write-Host "`nSetup complete! Restart Laravel server." -ForegroundColor Green
pause
