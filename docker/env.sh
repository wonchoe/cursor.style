#!/bin/bash

if [ ! -f /var/www/cursor.style/.env ]; then
	echo "Creating .env for cursor.style"
	cp /tmp/cursor.style.env /var/www/cursor.style/.env
	cd /var/www/cursor.style/ || { echo "Failed to change directory"; exit 1; }
	mkdir -p storage/{app,framework,logs}
	mkdir -p storage/framework/{sessions,views,cache}
	chmod -R 777 storage
	php artisan optimize
else
  echo ".env for cursor.style already exists"
fi

