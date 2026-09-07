FROM php:8.4-apache

# Systemabhängigkeiten installieren
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    unzip \
    git \
    curl \
    libpq-dev \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP-Erweiterungen nacheinander installieren (vermeidet Race-Conditions)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd
RUN docker-php-ext-install mbstring pdo pdo_mysql zip exif bcmath intl
RUN docker-php-ext-install opcache

# Apache mod_rewrite aktivieren und DocumentRoot auf src/public setzen
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/src/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Composer installieren
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Node.js installieren (für Asset-Build)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Nur Composer-Dateien zuerst kopieren, damit der Layer gecachtet werden kann
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && composer clear-cache

# Projektdateien kopieren
COPY . .

# Composer-Autoloader und Laravel-Pakete neu aufbauen
RUN composer dump-autoload --optimize --no-dev \
    && cd src && php artisan package:discover --ansi

# Frontend-Assets bauen
RUN cd src && npm install --ignore-scripts && npm run build

# Berechtigungen für Laravel-Cache/Storage
RUN chown -R www-data:www-data /var/www/html/src/storage /var/www/html/src/bootstrap/cache \
    && chmod -R 775 /var/www/html/src/storage /var/www/html/src/bootstrap/cache

# Entrypoint kopieren und ausführbar machen
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
