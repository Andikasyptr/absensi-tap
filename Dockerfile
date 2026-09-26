FROM php:8.3-apache

# Install dependencies sistem, ekstensi PHP, dan library font/image untuk Dompdf
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libfontconfig1 \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip

# Clear cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP yang dibutuhkan Laravel & Dompdf
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache intl

# Install Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tentukan working directory
WORKDIR /var/www/html

# Salin konfigurasi VirtualHost Apache kustom (sesuai path .docker/vhost.conf)
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Aktifkan modul mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# Salin seluruh file project ke dalam container
COPY . /var/www/html

# Install dependencies composer dengan batasan memori tak terbatas & verbose untuk melacak error
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction --verbose || exit 1

# Berikan izin akses (permission) ke folder storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Atur port dinamis (Render menggunakan port 10000 secara default)
ENV PORT=10000
RUN sed -i -e 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Command otomatis untuk clear cache, migrasi database, dan menjalankan Apache
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    apache2-foreground