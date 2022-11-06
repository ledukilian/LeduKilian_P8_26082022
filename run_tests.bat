@echo off
title PHPUnit Tests Runner
goto :run

:run
cls
mode con cols=85 lines=50
::echo [INFO] Creating database for test environnement
::php bin/console doctrine:database:create --env test
::echo.
::echo [INFO] Creating schema for test environnement
::php bin/console doctrine:schema:create --env test
::echo.
echo ### Purging database and loading fixtures for test environnement
php bin/console doctrine:fixtures:load --env test --no-interaction
echo.
echo.
echo ### Running tests
echo.
php bin/phpunit --coverage-html public/test_coverage
echo.
echo.
echo ### Tests completed successfully, press enter to run again
pause > nul
goto :run