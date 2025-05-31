@echo off
echo Campus Connect Database Import Tool
echo ===================================
echo.
echo This script will import the Campus Connect database using MySQL command line.
echo Make sure your XAMPP MySQL service is running before proceeding.
echo.
echo Press any key to continue or CTRL+C to cancel...
pause > nul

set MYSQL_PATH=C:\xampp\mysql\bin
set SQL_FILE=database\reset_and_import.sql

echo.
echo Importing database...
"%MYSQL_PATH%\mysql" -u root < "%SQL_FILE%"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo Database imported successfully!
    echo.
    echo You can now access:
    echo - Website: http://localhost/campus-connect/
    echo - Admin Panel: http://localhost/campus-connect/admin/
    echo.
    echo Admin login credentials:
    echo Username: admin
    echo Password: admin123
) else (
    echo.
    echo Error importing database.
    echo Please make sure MySQL service is running and try again.
    echo.
    echo Alternatively, use the web interface:
    echo http://localhost/campus-connect/import_database.php
)

echo.
echo Press any key to exit...
pause > nul