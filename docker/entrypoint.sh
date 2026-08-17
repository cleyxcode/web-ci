#!/bin/sh
set -eu

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

# Jalankan migration idempotent saat container hidup agar database baru maupun
# database volume lama otomatis mengikuti schema dan seeder demo terbaru.
if [ "${KKN_SKIP_MIGRATIONS:-0}" != "1" ] && [ -x /var/www/html/spark ]; then
    php /var/www/html/spark migrate --all || echo "Migration dilewati; database belum siap."
fi

exec apache2-foreground
