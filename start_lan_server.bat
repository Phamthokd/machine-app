@echo off
set "PATH=C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\nodejs\node-v22;%PATH%"
echo ========================================================
echo   KHOI DONG SERVER NOI BO (LAN)
echo ========================================================
echo.
echo Dang khoi dong server...
echo (Giu cua so nay luon mo de web hoat dong)
echo Truy cap tai: http://localhost:8000
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause
