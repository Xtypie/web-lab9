FROM php:8.2-fpm
RUN apt-get update && apt-get install -y git unzip curl \
    && docker-php-ext-install pdo pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY composer.json ./
RUN composer install --no-interaction --prefer-dist
COPY . .
CMD ["php-fpm"]