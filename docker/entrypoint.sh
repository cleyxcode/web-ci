#!/bin/sh
set -eu

if [ ! -f /var/www/html/.env ]; then
    cp /usr/local/share/kkn-monitoring.env /var/www/html/.env
fi

mkdir -p \
    /var/www/html/writable/cache \
    /var/www/html/writable/logs \
    /var/www/html/writable/session \
    /var/www/html/writable/uploads \
    /var/www/html/writable/debugbar \
    /var/www/html/public/uploads/logbook \
    /var/www/html/public/uploads/laporan

chown -R www-data:www-data \
    /var/www/html/writable \
    /var/www/html/public/uploads
chmod -R ug+rwX \
    /var/www/html/writable \
    /var/www/html/public/uploads

exec apache2-foreground
