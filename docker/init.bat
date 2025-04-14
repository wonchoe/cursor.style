@echo off
echo ✅ Installing Cloudflare Origin CA Root Certificate...

certutil -addstore "Root" "%~dp0origin_ca_rsa_root.pem"

echo ✅ Done.
pause
