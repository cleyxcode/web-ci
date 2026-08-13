#!/bin/bash
set -e

# Install CI4 jika belum ada
if [ ! -f "/var/www/html/composer.json" ]; then
    echo ">>> Installing CodeIgniter 4..."
    composer create-project codeigniter4/appstarter /var/www/html --no-interaction
    echo ">>> CI4 installed!"
fi

# Install vendor jika belum ada (app sudah punya composer.json)
if [ -f "/var/www/html/composer.json" ] && [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo ">>> Installing Composer dependencies..."
    composer install --no-interaction --working-dir=/var/www/html
    echo ">>> Dependencies installed!"
fi

# Copy .env jika belum ada
if [ ! -f "/var/www/html/.env" ]; then
    cp /var/www/html/env /var/www/html/.env
    sed -i 's/^# CI_ENVIRONMENT = production$/CI_ENVIRONMENT = development/' /var/www/html/.env
    sed -i "s|^# app.baseURL = ''$|app.baseURL = 'http://localhost:8083/'|" /var/www/html/.env
    sed -i 's/^# database.default.hostname = localhost$/database.default.hostname = db/' /var/www/html/.env
    sed -i 's/^# database.default.database = ci4$/database.default.database = kkn_tematik/' /var/www/html/.env
    sed -i 's/^# database.default.username = root$/database.default.username = kkn_user/' /var/www/html/.env
    sed -i 's/^# database.default.password = root$/database.default.password = kkn_pass/' /var/www/html/.env
    sed -i 's/^# database.default.DBDriver = MySQLi$/database.default.DBDriver = MySQLi/' /var/www/html/.env
    echo ">>> .env configured!"
fi

# Set permission (writable harus bisa ditulis www-data)
mkdir -p /var/www/html/writable/{cache,session,logs,uploads,debugbar} /var/www/html/public/uploads/{logbook,laporan}
chown -R www-data:www-data /var/www/html/writable /var/www/html/public/uploads
chmod -R ug+rwX /var/www/html/writable /var/www/html/public/uploads

# Jalankan Apache
echo ">>> Starting Apache..."
apache2-foreground
