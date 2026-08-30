@echo off
echo ==========================================
echo  Finding Your Old Working PHP Install
echo ==========================================
echo.

REM Search for php.exe on entire system
echo Searching for PHP installations...
echo This may take a minute...
echo.

REM Check common locations first
set found=0

if exist "C:\xampp\php\php.exe" (
    echo [FOUND] C:\xampp\php\php.exe
    "C:\xampp\php\php.exe" -v
    set found=1
)

if exist "C:\wamp64\bin\php\php.exe" (
    echo [FOUND] C:\wamp64\bin\php\php.exe
    "C:\wamp64\bin\php\php.exe" -v
    set found=1
)

if exist "C:\laragon\bin\php\php.exe" (
    echo [FOUND] C:\laragon\bin\php\php.exe
    "C:\laragon\bin\php\php.exe" -v
    set found=1
)

if exist "C:\Program Files\php\php.exe" (
    echo [FOUND] C:\Program Files\php\php.exe
    "C:\Program Files\php\php.exe" -v
    set found=1
)

if exist "C:\php7\php.exe" (
    echo [FOUND] C:\php7\php.exe
    "C:\php7\php.exe" -v
    set found=1
)

if exist "C:\php8\php.exe" (
    echo [FOUND] C:\php8\php.exe
    "C:\php8\php.exe" -v
    set found=1
)

if %found%==0 (
    echo.
    echo ❌ No other PHP found. You need to reinstall PHP.
    echo.
    echo Download the correct Windows binary (NOT source):
    echo https://windows.php.net/downloads/releases/php-8.3.11-nts-Win32-vs16-x64.zip
    echo.
    echo Then extract it to C:\php (overwrite everything)
)

echo.
pause
