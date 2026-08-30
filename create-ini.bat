@echo off
echo ==========================================
echo  Creating php.ini for Laravel
echo ==========================================
echo.

REM Copy development ini
copy "C:\php\php.ini-development" "C:\php\php.ini"

echo.
echo Enabling required extensions...

REM Use PowerShell to enable extensions
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension_dir = \"ext\"', 'extension_dir = \"C:\\php\\ext\"' | Set-Content 'C:\php\php.ini'"

powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=openssl', 'extension=openssl' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=pdo_sqlite', 'extension=pdo_sqlite' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=sqlite3', 'extension=sqlite3' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=mbstring', 'extension=mbstring' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=curl', 'extension=curl' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=fileinfo', 'extension=fileinfo' | Set-Content 'C:\php\php.ini'"

echo.
echo ✅ php.ini created and configured!
echo.
echo Testing PHP...
"C:\php\php.exe" -m | findstr /i "sqlite openssl mbstring curl"

echo.
echo Now restart Laravel:
echo   1. Press Ctrl+C to stop server
echo   2. Run: C:\php\php.exe artisan serve
echo.
pause
