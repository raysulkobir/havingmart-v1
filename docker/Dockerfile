FROM php:7.4-apache

# Debian bullseye is EOL and has moved to archive.debian.org, so repoint apt there.
RUN sed -i \
        -e 's|http://deb.debian.org/debian-security|http://archive.debian.org/debian-security|g' \
        -e 's|http://deb.debian.org/debian|http://archive.debian.org/debian|g' \
        -e '/bullseye-updates/d' \
        /etc/apt/sources.list

# System libraries + image-optimizer binaries used by spatie/laravel-image-optimizer
RUN apt-get -o Acquire::Check-Valid-Until=false update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libcurl4-openssl-dev \
        jpegoptim optipng pngquant gifsicle \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        bcmath \
        mbstring \
        exif \
        curl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer (pinned major, from the official image)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Apache: Laravel-style rewrite + document root at project root
RUN a2enmod rewrite headers
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

ENV COMPOSER_NO_INTERACTION=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    GIT_TERMINAL_PROMPT=0

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
