@echo off
echo ==========================================
echo  Fixing PHP SQLite Extension
echo ==========================================
echo.

REM Copy php.ini-development to php.ini
if not exist "C:\php\php.ini" (
    echo Creating php.ini from php.ini-development...
    copy "C:\php\php.ini-development" "C:\php\php.ini"
)

echo Enabling SQLite extensions...

REM Enable sqlite3 extension
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=sqlite3', 'extension=sqlite3' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=pdo_sqlite', 'extension=pdo_sqlite' | Set-Content 'C:\php\php.ini'"

echo.
echo ✅ SQLite extensions enabled!
echo.
echo Testing PHP with SQLite...
"C:\php\php.exe" -r "phpinfo();" | findstr /i "sqlite"

echo.
echo Now restart your Laravel server:
echo   1. Press Ctrl+C to stop current server
echo   2. Run: C:\php\php.exe artisan serve
echo.
pause
