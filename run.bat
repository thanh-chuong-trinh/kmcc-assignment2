@echo off
rem run.bat - starts MySQL and PHP from XAMPP and opens the site. Close this window to stop the website.
rem If XAMPP is not in C:\xampp or D:\xampp, run:  set XAMPP_DIR=E:\path\to\xampp  before run.bat

if not defined XAMPP_DIR if exist C:\xampp\php\php.exe set XAMPP_DIR=C:\xampp
if not defined XAMPP_DIR if exist D:\xampp\php\php.exe set XAMPP_DIR=D:\xampp
if not defined XAMPP_DIR (
  echo XAMPP was not found. Install XAMPP or set XAMPP_DIR to its folder.
  pause
  exit /b 1
)

rem Start MySQL unless something is already listening on port 3306.
netstat -ano | %SystemRoot%\System32\find.exe ":3306 " | %SystemRoot%\System32\find.exe "LISTENING" >nul
if errorlevel 1 (
  echo Starting MySQL...
  start "" /min "%XAMPP_DIR%\mysql\bin\mysqld.exe" --defaults-file="%XAMPP_DIR%\mysql\bin\my.ini" --standalone
  ping -n 7 127.0.0.1 >nul
)

rem Create the database and tables (safe to run again).
"%XAMPP_DIR%\mysql\bin\mysql.exe" -u root < "%~dp0database.sql"

echo.
echo Site running at http://localhost:8089/index.php
start "" http://localhost:8089/index.php
"%XAMPP_DIR%\php\php.exe" -S localhost:8089 -t "%~dp0project2"
