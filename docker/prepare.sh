#!/bin/bash

function deploy_composer {
    cd "$1" || exit

    echo "Installing composer dependencies in $1"

    composer install --no-dev --no-interaction --prefer-dist || {
        echo "Install failed, trying composer update..."
        composer update --no-dev --no-interaction --prefer-dist
    }

    rm -f public/storage public/collection public/cursors public/pointers
    php artisan storage:link --force
}

deploy_composer /var/www/cursor.style
