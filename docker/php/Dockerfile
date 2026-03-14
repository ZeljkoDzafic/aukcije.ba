# ===================================
# AUKCIJSKA PLATFORMA - PHP DOCKERFILE
# ===================================
# Based on Laravel Sail with additional extensions
# PHP 8.3 with all required extensions

FROM php:8.3-fpm

# Arguments
ARG USER_ID=1000
ARG GROUP_ID=1000

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Europe/Sarajevo

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libssl-dev \
    libbz2-dev \
    libreadline-dev \
    libxslt1-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    postgresql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PECL extensions
RUN pecl install redis \
    && pecl install xdebug \
    && docker-php-ext-enable redis xdebug

# Install PHP extensions (core, no extra configure needed)
RUN docker-php-ext-install -j$(nproc) pdo_pgsql pgsql mbstring exif bcmath zip soap xsl

# Install intl (requires configure)
RUN docker-php-ext-configure intl && docker-php-ext-install -j$(nproc) intl

# Install GD (requires configure with image format paths)
RUN docker-php-ext-configure gd \
        --with-freetype=/usr/include/freetype2 \
        --with-jpeg=/usr \
        --with-webp=/usr \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get update && apt-get install -y --no-install-recommends \
    libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN groupadd --gid ${GROUP_ID} docker \
    && useradd -u ${USER_ID} -g docker -m docker -s /bin/bash \
    && usermod -aG sudo docker

# Set working directory
WORKDIR /var/www/html

# Copy PHP configuration
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

# Copy entrypoint script
COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start PHP-FPM
CMD ["php-fpm"]
