#!/bin/bash
echo "▶ Running prepare.sh..."
cd /var/www && /var/prepare.sh

service php8.3-fpm start &
nginx -g 'daemon off;'

tail -f /dev/null