FROM php:8.4-fpm

# Instalar extensiones y herramientas básicas
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql intl opcache zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /srv/app

# Crear usuario no-root (opcional pero recomendado)
RUN useradd -m -u 1000 symfony && chown -R symfony:symfony /srv/app

USER symfony

CMD ["php-fpm"]
