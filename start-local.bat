@echo off
echo ==========================================
echo  Costikyan Laravel Local Server Starter
echo ==========================================
echo.

REM Use PHP from C:\php
set PHP_PATH=C:\php\php.exe

if not exist "%PHP_PATH%" (
    echo ERROR: PHP not found at C:\php\php.exe!
    echo Please extract your PHP download to C:\php folder
    pause
    exit /b 1
)

echo ✅ Found PHP at %PHP_PATH%
echo.
echo Starting Laravel server...
echo.

"%PHP_PATH%" artisan serve --host=127.0.0.1 --port=8000

pause
