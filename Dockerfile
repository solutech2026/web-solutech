FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    curl git unzip nodejs npm libpq-dev && apt-get clean

RUN docker-php-ext-install pdo_pgsql pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs
RUN npm install && npm run build
RUN php artisan optimize
RUN chmod -R 775 storage bootstrap/cache public/build

EXPOSE ${PORT:-8000}

# Usar PORT de Railway, si no existe usar 8000
CMD php artisan migrate --force && php -S 0.0.0.0:${PORT:-8000} -t public