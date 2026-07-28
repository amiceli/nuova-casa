FROM node:22 AS builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

FROM dunglas/frankenphp:1.12-php8.4

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl unzip git \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pcntl opcache pdo_mysql pdo_pgsql pgsql intl zip gd exif ftp bcmath redis

RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit_buffer_size=256M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/custom.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY . .

COPY --from=builder /app/public/build /app/public/build

RUN composer install --prefer-dist --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# caddy serves /app/public on plain http, the proxy in front terminates the tls
ENV SERVER_NAME=:8000

EXPOSE 8000
