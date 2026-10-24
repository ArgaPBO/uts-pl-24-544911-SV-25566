
# Pin both FrankenPHP and PHP versions.
FROM dunglas/frankenphp:1-php8.3.36-bookworm

WORKDIR /app

# Install PHP extensions commonly needed by Laravel.
RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    bcmath \
    intl \
    zip \
    pcntl \
    opcache

# Install Composer.
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Copy application source.
COPY . .

COPY Caddyfile /etc/caddy/Caddyfile

# Install production dependencies.
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

# Laravel writable directories.
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Production PHP configuration.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV SERVER_NAME=:8000

EXPOSE 8000

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
