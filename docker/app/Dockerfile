FROM composer:2.2.24 AS composer
FROM php:8.3-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    supervisor \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    cron \
    nano

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# Configure cron
RUN crontab -l | { cat; echo "* * * * * su -c '/usr/local/bin/php /var/www/artisan schedule:run >> /var/log/schedule.log 2>&1'"; } | crontab -

# Set working directory
WORKDIR /var/www

# Copy custom configurations
COPY nginx.conf /etc/nginx/sites-enabled/default
COPY php.ini /usr/local/etc/php/conf.d/php.ini
COPY opcache.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
