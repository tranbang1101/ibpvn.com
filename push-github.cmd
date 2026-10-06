@echo off

cd /d "%~dp0"

echo.
echo === UPDATE GITHUB ===
echo.

git add .
git commit -m "Update ibpvn.com"
git push

echo.
echo === HOAN TAT ===
pause