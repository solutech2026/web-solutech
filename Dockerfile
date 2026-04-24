FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    curl git unzip nodejs npm libpq-dev && apt-get clean

RUN docker-php-ext-install pdo_pgsql pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Generar .env directamente
RUN echo "APP_ENV=production" > .env && \
    echo "APP_DEBUG=true" >> .env && \
    echo "APP_KEY=base64:6eXVyWJzyuNO7YYj3v/G+L5kCNc0njU+O0hbIPLcDuk=" >> .env && \
    echo "APP_URL=https://web-solutech-production.up.railway.app" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "DATABASE_URL=postgresql://postgres:dhxHXcZXJgRxQYbgrNoiyXqbnlKMPBvu@postgres-production-845e6.up.railway.app:5432/railway?sslmode=require" >> .env

RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs
RUN npm install && npm run build
RUN php artisan config:cache
RUN php artisan optimize
RUN chmod -R 775 storage bootstrap/cache public/build

EXPOSE ${PORT:-8000}

CMD php artisan migrate --force && php -S 0.0.0.0:${PORT:-8000} -t public