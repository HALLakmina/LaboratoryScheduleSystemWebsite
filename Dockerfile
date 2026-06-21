FROM composer:2 AS vendor
WORKDIR /app
COPY Backend/composer.json Backend/composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo_mysql \
    && a2enmod rewrite

# App keeps the same "LaboratoryScheduleSystemWebsite" subfolder it used under
# XAMPP htdocs, since Frontend/config.php and Backend/.htaccess hardcode that path.
WORKDIR /var/www/html/LaboratoryScheduleSystemWebsite

COPY . .
COPY --from=vendor /app/vendor ./Backend/vendor

# DocumentRoot (/var/www/html) has no index of its own — only the app
# subfolder above. Without this, http://localhost/ hits Apache's
# "No matching DirectoryIndex found" / autoindex-forbidden error.
COPY docker-root-index.php /var/www/html/index.php

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
