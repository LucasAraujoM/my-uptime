@echo off
:loop
php artisan schedule:run
goto loop