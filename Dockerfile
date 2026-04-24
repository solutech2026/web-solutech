FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    curl git unzip nodejs npm libpq-dev nginx && apt-get clean

RUN docker-php-ext-install pdo_pgsql pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY nginx.conf /etc/nginx/sites-enabled/default

WORKDIR /app
COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs
RUN npm install && npm run build
RUN php artisan optimize
RUN chmod -R 775 storage bootstrap/cache public/build

EXPOSE 8000

CMD php-fpm -D && nginx -g 'daemon off;'