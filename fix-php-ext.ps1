# Fix missing PHP extensions
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Fixing Missing PHP Extensions" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Check if ext folder is empty
$extFiles = Get-ChildItem -Path "C:\php\ext" -ErrorAction SilentlyContinue
if ($extFiles.Count -eq 0) {
    Write-Host "❌ C:\php\ext is EMPTY! Extensions are missing." -ForegroundColor Red
    Write-Host ""
    Write-Host "You need to re-download PHP with extensions included." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Option 1: Download correct PHP manually:" -ForegroundColor Cyan
    Write-Host "   https://windows.php.net/downloads/releases/php-8.3.11-nts-Win32-vs16-x64.zip" -ForegroundColor White
    Write-Host ""
    Write-Host "Option 2: Use XAMPP (easiest):" -ForegroundColor Cyan
    Write-Host "   https://www.apachefriends.org/download.html" -ForegroundColor White
    Write-Host ""
    Write-Host "After installing, extract to C:\php with extensions in C:\php\ext\" -ForegroundColor Yellow
} else {
    Write-Host "✅ Extensions exist in C:\php\ext" -ForegroundColor Green
    Write-Host "Found $($extFiles.Count) extension files" -ForegroundColor White
}

Write-Host ""
Write-Host "Current PHP info:" -ForegroundColor Cyan
php -v
Write-Host ""
Write-Host "Loaded extensions:" -ForegroundColor Cyan
php -m | Where-Object { $_ -match "sqlite|openssl|mbstring|curl" }

pause
