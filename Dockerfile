FROM php:8.3-apache

# Install required system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    default-mysql-client \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        intl \
        zip \
        opcache \
        mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module for Yii2 clean URLs
RUN a2enmod rewrite

# Configure Apache DocumentRoot to point to /var/www/html/web
ENV APACHE_DOCUMENT_ROOT=/var/www/html/web
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache directory access for Yii2
RUN echo '<Directory /var/www/html/web>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/yii2.conf \
    && a2enconf yii2

# Install Composer from official Composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Pre-install composer dependencies for build layer caching
COPY composer.json ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copy application source code
COPY . .

# Ensure runtime directories exist with appropriate permissions
RUN mkdir -p runtime web/assets \
    && chown -R www-data:www-data runtime web/assets \
    && chmod -R 777 runtime web/assets

EXPOSE 80

CMD ["sh", "-c", "if [ ! -d /var/www/html/vendor/yiisoft ]; then composer install --no-interaction --prefer-dist --optimize-autoloader; fi && mkdir -p runtime web/assets && chmod -R 777 runtime web/assets && apache2-foreground"]
