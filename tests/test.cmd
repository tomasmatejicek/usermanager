@echo off
php -f "%~dp0..\vendor\nette\tester\src\tester.php" %~dp0 -c "%~dp0php.ini" 

timeout /T 15