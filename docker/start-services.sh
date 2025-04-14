#!/bin/bash
set -e

echo "▶ Running prepare.sh..."
if [[ -f /var/prepare.sh ]]; then
    /var/prepare.sh
else
    echo "❌ prepare.sh not found"
    exit 1
fi

echo "▶ Starting PHP-FPM..."
service php8.3-fpm start

echo "▶ Testing NGINX config..."
nginx -t

echo "▶ Starting NGINX..."
nginx -g 'daemon off;'