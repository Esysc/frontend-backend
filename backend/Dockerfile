FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    curl \
    git \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock* ./

RUN composer require symfony/runtime \
    && composer install --no-dev --optimize-autoloader

# Copy data files (stations/distances will be removed after database load, openapi.yml stays)
COPY stations.json distances.json openapi.yml ./

COPY . .

RUN chmod +x docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["./docker-entrypoint.sh"]
