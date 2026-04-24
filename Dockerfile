FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    curl git unzip nodejs npm libpq-dev && apt-get clean

RUN docker-php-ext-install pdo_pgsql pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Generar .env con los valores reales de Railway
RUN echo "APP_NAME=Solutech" > .env
RUN echo "APP_ENV=production" >> .env
RUN echo "APP_DEBUG=false" >> .env
RUN echo "APP_URL=${APP_URL}" >> .env
RUN echo "APP_KEY=${APP_KEY}" >> .env
RUN echo "APP_LOCALE=es" >> .env
RUN echo "APP_FALLBACK_LOCALE=en" >> .env
RUN echo "APP_FAKER_LOCALE=es_ES" >> .env
RUN echo "APP_MAINTENANCE_DRIVER=file" >> .env
RUN echo "BCRYPT_ROUNDS=12" >> .env
RUN echo "LOG_CHANNEL=stack" >> .env
RUN echo "LOG_STACK=single" >> .env
RUN echo "LOG_DEPRECATIONS_CHANNEL=null" >> .env
RUN echo "LOG_LEVEL=error" >> .env
RUN echo "DB_CONNECTION=pgsql" >> .env
RUN echo "DB_HOST=postgres.railway.internal" >> .env
RUN echo "DB_PORT=5432" >> .env
RUN echo "DB_DATABASE=railway" >> .env
RUN echo "DB_USERNAME=postgres" >> .env
RUN echo "DB_PASSWORD=dhxHXcZXJgRxQYbgrNoiyXqbnlKMPBvu" >> .env
RUN echo "SESSION_DRIVER=database" >> .env
RUN echo "SESSION_LIFETIME=120" >> .env
RUN echo "SESSION_ENCRYPT=false" >> .env
RUN echo "SESSION_PATH=/" >> .env
RUN echo "SESSION_DOMAIN=null" >> .env
RUN echo "BROADCAST_CONNECTION=log" >> .env
RUN echo "FILESYSTEM_DISK=local" >> .env
RUN echo "QUEUE_CONNECTION=database" >> .env
RUN echo "CACHE_STORE=database" >> .env
RUN echo "REDIS_CLIENT=phpredis" >> .env
RUN echo "REDIS_HOST=127.0.0.1" >> .env
RUN echo "REDIS_PASSWORD=null" >> .env
RUN echo "REDIS_PORT=6379" >> .env
RUN echo "MAIL_MAILER=smtp" >> .env
RUN echo "MAIL_HOST=smtp.gmail.com" >> .env
RUN echo "MAIL_PORT=587" >> .env
RUN echo "MAIL_USERNAME=soporteitsolutech@gmail.com" >> .env
RUN echo "MAIL_PASSWORD=wuavqhkmifbwyjxb" >> .env
RUN echo "MAIL_ENCRYPTION=tls" >> .env
RUN echo "MAIL_FROM_ADDRESS=soporteitsolutech@gmail.com" >> .env
RUN echo "MAIL_FROM_NAME=Solutech" >> .env

RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs
RUN npm install && npm run build
RUN php artisan config:clear
RUN php artisan config:cache
RUN php artisan optimize
RUN chmod -R 775 storage bootstrap/cache public/build

EXPOSE ${PORT:-8000}

CMD php artisan migrate --force && php -S 0.0.0.0:${PORT:-8000} -t public