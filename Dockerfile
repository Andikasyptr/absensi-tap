FROM php:8.3-apache

# 1. Install sistem dependensi & ekstensi PHP
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

# 2. Setup Working Directory
WORKDIR /var/www/html

# 3. Konfigurasi Apache vhost
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# 4. Salin SELURUH project termasuk folder vendor lokal yang sudah jadi
COPY . /var/www/html

# 5. Set permission folder storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Sesuaikan port untuk Render (10000)
ENV PORT=10000
RUN sed -i -e 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# 7. Jalankan clear cache, migrasi database, lalu start Apache
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    apache2-foreground