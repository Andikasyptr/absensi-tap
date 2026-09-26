FROM php:8.2-apache

# Install dependencies sistem & ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# Install Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tentukan working directory
WORKDIR /var/www/html

# Salin konfigurasi VirtualHost Apache kustom
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Aktifkan modul mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# Salin seluruh file project ke dalam container
COPY . /var/www/html

# Install dependencies composer (Production mode)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Berikan izin akses (permission) ke folder storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Atur port dinamis (Render menggunakan port 10000 secara default)
ENV PORT=10000
RUN sed -i -e 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Command otomatis untuk clear/optimize cache dan menjalankan Apache
CMD php artisan config:cache && \
    php artisan route:cache && \
    apache2-foreground