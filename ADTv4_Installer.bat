set "params=%*"
cd /d "%~dp0" && ( if exist "%temp%\getadmin.vbs" del "%temp%\getadmin.vbs" ) && fsutil dirty query %systemdrive% 1>nul 2>nul || (  echo Set UAC = CreateObject^("Shell.Application"^) : UAC.ShellExecute "cmd.exe", "/k cd ""%~sdp0"" && %~s0 %params%", "", "runas", 1 >> "%temp%\getadmin.vbs" && "%temp%\getadmin.vbs" && exit /B )


set zip=resources\zip\7z.exe
set xampp=resources\xampp.zip
set target=C:\
set target_xampp=C:\xampp\
set target_apache=C:\xampp\apache\
set target_mysql=C:\xampp\mysql\
set database=resources\database\testadt.sql
set apache_port=88
set adt_url=http://localhost:%apache_port%/ADT
set mysql_port=3307
set mysql_user=root
set mysql_passwd=root


::Change Color
color a

::Clear Screen
cls

::Display Menu
echo ADTv4.0 Installer Starting...
echo *******************************

echo 'Do you want to continue to install ADTv4.0.0?'
pause

::decompress using xampp


%zip% x %xampp% -o%target% *.* -r

cls

echo 1.webADT Extraction Complete
echo *******************************

::install & start apache service
cd %CD%
%target_apache%bin\httpd.exe -k install -n "Apache2.4"
echo Now we Start Apache2.4 :)
%target_apache%bin\httpd.exe

echo 2.Apache Services Complete
echo *******************************

::install & start mySQL service

echo Installing MySQL as an Service 
%target_mysql%bin\mysqld --install mysql --defaults-file="%target_mysql%\bin\my.ini"
echo Try to start the MySQL deamon as service ... 
net start mysql 

echo 3.MySQL Services Complete
echo *******************************

echo Copying New ADT database
%target_mysql%bin\mysql.exe -u %mysql_user% -p%mysql_passwd% -P %mysql_port% testadt<%database%


echo Rewritting back data to the database
%target_mysql%bin\mysql.exe -u %mysql_user% -p%mysql_passwd% -P %mysql_port% testadt<%backupDir%\adt.sql

echo 4.Database initialization Complete
echo *******************************

echo 5.Starting browser
start %adt_url%

pause







