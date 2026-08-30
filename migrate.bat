@echo off
echo Running Laravel migrations...
cd /d "E:\prime-smile-lab-67-main\github\rug-website"
C:\php\php.exe artisan migrate --force
echo.
echo Done!
pause
