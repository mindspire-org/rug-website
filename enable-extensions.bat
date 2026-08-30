@echo off
echo ==========================================
echo  Enabling PHP Extensions for Laravel
echo ==========================================
echo.

REM Check if php.ini exists
if not exist "C:\php\php.ini" (
    echo Creating php.ini from php.ini-development...
    copy "C:\php\php.ini-development" "C:\php\php.ini"
)

echo Enabling required extensions...

REM Enable all required Laravel extensions
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=openssl', 'extension=openssl' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=mbstring', 'extension=mbstring' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=curl', 'extension=curl' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=fileinfo', 'extension=fileinfo' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=gd', 'extension=gd' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=intl', 'extension=intl' | Set-Content 'C:\php\php.ini'"

echo.
echo ✅ All extensions enabled!
echo.
echo Verifying extensions...
echo.
"C:\php\php.exe" -m | findstr /i "openssl mbstring curl fileinfo gd intl sqlite pdo"

echo.
echo ==========================================
echo IMPORTANT: Restart your Laravel server!
echo ==========================================
echo 1. Press Ctrl+C to stop current server
echo 2. Run: C:\php\php.exe artisan serve
echo.
pause
