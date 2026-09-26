FROM php:8.3-apache

# 1. Install sistem dependensi lengkap untuk Laravel 13 & Dompdf
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
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install dan configure seluruh ekstensi PHP yang dibutuhkan (Termasuk dom, xml, intl, gd)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        opcache \
        intl \
        dom \
        xml \
        xmlwriter

# 3. Install Composer resmi terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 4. Salin konfigurasi Apache vhost
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# 5. Salin composer files terlebih dahulu untuk caching layer Docker yang optimal
COPY composer.json composer.lock ./

# 6. Jalankan composer dengan mengabaikan platform check demi keamanan build di container
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# 7. Salin sisa file project Laravel Anda
COPY . /var/www/html

# 8. Set permission folder storage & cache agar writable
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Penyesuaian port dinamis untuk Render (Port 10000)
ENV PORT=10000
RUN sed -i -e 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# 10. Eksekusi command clear cache, migrasi database Aiven, lalu jalankan Apache
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    apache2-foreground