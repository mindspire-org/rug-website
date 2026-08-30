@echo off
echo Extracting PHP to C:\php...

REM Find PHP zip in Downloads
for %%f in (%USERPROFILE%\Downloads\php*.zip) do (
    echo Found: %%f
    echo Extracting...
    powershell -Command "Expand-Archive -Path '%%f' -DestinationPath 'C:\php' -Force"
    echo.
    echo ✅ PHP extracted to C:\php
    echo.
    echo Files in C:\php:
    dir "C:\php" /b
    echo.
    pause
    exit /b 0
)

echo ❌ PHP zip file not found in Downloads!
echo Please download PHP from https://windows.php.net/download/
pause
exit /b 1
