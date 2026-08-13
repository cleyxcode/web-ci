FROM php:8.2-apache

# Install ekstensi PHP (intl wajib untuk CodeIgniter 4)
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip libpng-dev \
    libonig-dev libxml2-dev libicu-dev curl git \
    && docker-php-ext-install \
    pdo pdo_mysql mysqli mbstring intl \
    exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Konfigurasi Apache untuk CI4
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Script install CI4 otomatis saat container pertama kali jalan
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html
EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
