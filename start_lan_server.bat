@echo off
echo ========================================================
echo   KHOI DONG SERVER NOI BO (LAN)
echo ========================================================
echo.
echo Dia chi IP cua may nay la: 192.168.2.97 (Du kien)
echo Cac may khac co the truy cap qua: http://192.168.2.97:8000
echo.
echo Dang khoi dong server...
echo (Giu cua so nay luon mo de web hoat dong)
echo.

"C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan serve --host=0.0.0.0 --port=8000

pause
