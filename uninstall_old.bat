set "params=%*"
cd /d "%~dp0" && ( if exist "%temp%\getadmin.vbs" del "%temp%\getadmin.vbs" ) && fsutil dirty query %systemdrive% 1>nul 2>nul || (  echo Set UAC = CreateObject^("Shell.Application"^) : UAC.ShellExecute "cmd.exe", "/k cd ""%~sdp0"" && %~s0 %params%", "", "runas", 1 >> "%temp%\getadmin.vbs" && "%temp%\getadmin.vbs" && exit /B )

set path=C:\xampp\mysql\bin\
set backupDir=C:\Users\%USERNAME%\Desktop
set windir = "C:\xampp\mysql\"
set apache=C:\xampp\apache\bin\
set cmd=C:\WINDOWS\system32\

echo 'Backing up previous ADT Database ....'
echo 'Started mysql dump...'
%path%mysqldump --no-create-info -uroot -proot  -P3307 testadt >  %backupDir%\adt.sql
echo 'Backup Done....'

echo 'Shutting down mysql...'
%path%mysqladmin -u root -proot -P 3307 shutdown

echo now stopping MySQL when it runs
echo off
%cmd%net stop "mysql"
%cmd%ping -n 11 127.0.0.1 > nul
echo Uninstalling MySql-Service
%path%mysqld --remove mysql

%cmd%net stop "Apache2.4"
%cmd%ping -n 11 127.0.0.1 > nul


REM echo Time to say good bye to Apache2.4 :(
%apache%httpd -k uninstall
pause

echo "Renaming ADT Folder for backup..."
taskkill /F /FI "IMAGENAME eq xampp-control.exe"
RENAME "C:\xampp" "xampp-ADTv3.4.2"

echo 'Renaming Complete...'

pause