# Base image
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install --no-install-recommends -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    mariadb-server \
    mariadb-client \
    nginx && \
    rm -rf /var/lib/apt/lists/*

# Add docker-php-extension-installer script
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install php extensions
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    @composer \
    bcmath \
    mbstring \
    gd \
    exif \
    intl \
    pdo_mysql \
    pcntl

# Copy nginx configuration file
COPY docker/config/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/config/nginx/default.conf /etc/nginx/sites-enabled/default.conf
COPY docker/config/php/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/config/php/php.ini /usr/local/etc/php/php.ini

# Set root password
RUN service mariadb start && \
    mysql -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('root');" && \
    mysql -e "CREATE DATABASE currency_api;" && \
    mysql -e "FLUSH PRIVILEGES;"
COPY docker/config/mysql/.my.cnf /root

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html
COPY .env.prod /var/www/html/.env

# Install application dependencies
RUN composer install --no-interaction --no-scripts --prefer-dist

# Generate key
RUN service mariadb start && \
    php artisan key:generate && \
    php artisan migrate --seed --force

# Set storage and cache folder permissions
RUN chown www-data:www-data /var/www
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 8080
EXPOSE 8080

# Start PHP-FPM and Nginx
CMD service nginx start && service mariadb start && php-fpm
