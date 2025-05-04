#!/bin/bash
set -e
set -x

function deploy_composer {
    cd "$1" || exit 1

    if [ ! -d "vendor" ]; then
		composer config --global process-timeout 3600
		
        echo "▶ Installing composer dependencies in /var/www/cursor.style"
        timeout 3600 composer install --no-dev --no-interaction --prefer-dist -vvv
    else
        echo "▶ Vendor directory already exists, skipping composer install."
    fi

    echo "▶ Linking storage..."
    rm -f public/storage public/collection public/cursors public/pointers
    php artisan storage:link --force
}

deploy_composer /var/www/cursor.style


echo "▶ Starting PHP-FPM..."
php-fpm8.3 -F &
PHP_PID=$!

sleep 2
if ! ps -p $PHP_PID > /dev/null; then
    echo "❌ PHP-FPM не запустився"
    exit 1
fi

echo "▶ Starting NGINX..."
exec nginx -g 'daemon off;'
