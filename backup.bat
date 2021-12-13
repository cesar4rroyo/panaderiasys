@echo off
color 30
rem
rem Ejemplo de manejo de la fecha y hora actual - v2008-03-15
rem
rem Juego de caracteres ISO-8859-1 (Latin 1)
chcp 28591 > NUL


setlocal


set FECHA_ACTUAL=%DATE%
set HORA_ACTUAL=%TIME%


set ANO=%FECHA_ACTUAL:~6,4%
set MES=%FECHA_ACTUAL:~3,2%
set DIA=%FECHA_ACTUAL:~0,2%


set HORA=%HORA_ACTUAL:~0,2%
set MINUTOS=%HORA_ACTUAL:~3,2%
set SEGUNDOS=%HORA_ACTUAL:~6,2%
set CENTESIMAS=%HORA_ACTUAL:~9,2%


rem Si la hora tiene un sólo dígito, reemplazamos el espacio inicial por cero
set HORA=%HORA: =%
if %HORA% LSS 10 set HORA=0%HORA%


rem echo Fecha: %FECHA_ACTUAL%
rem echo Hora: %HORA_ACTUAL%


echo.
rem echo Día: %DIA%
rem echo Mes: %MES%
rem echo Año: %ANO%
echo.
rem echo Hora: %HORA%
rem echo Minutos: %MINUTOS%
rem echo Segundos: %SEGUNDOS%
rem echo Centésimas: %CENTESIMAS%
echo.

set pgpassword=lumbra*2017
set ARCHIVO=E:\Backup_Sistema\BACKUP_DBRESTAURANTE_%ANO%-%MES%-%DIA%_%HORA%-%MINUTOS%-%SEGUNDOS%.backup
"C:\Program Files (x86)\PostgreSQL\9.1\bin\"pg_dump.exe --host 127.0.0.1 --port 5432 --username postgres --format custom --blobs --verbose --file "%ARCHIVO%" "bdlumbra"


endlocal
echo.

