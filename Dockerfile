ARG PHP_VERSION

FROM composer:latest as composer

COPY composer.json composer.lock ./

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

FROM composer as composer-build

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative 

FROM php:${PHP_VERSION} as php-instl

RUN pecl install mongodb

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && docker-php-ext-install bcmath

RUN apt-get update && apt-get install -y git zip

FROM php-instl as development

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod 755 /usr/local/bin/composer \
    && chown www-data:www-data /var/www \
    && chmod g+s /var/www

WORKDIR /guesthouser-api

RUN chown -R www-data:www-data /guesthouser-api && chmod g+s /guesthouser-api

USER www-data

COPY composer.json composer.lock ./

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

FROM php-instl as production

COPY  php.ini "$PHP_INI_DIR/php.ini"

COPY --chown=www-data:www-data . /guesthouser-api

USER www-data

COPY --from=composer-build /app/vendor /guesthouser-api/vendor

RUN chmod -R 755 /guesthouser-api/public /guesthouser-api/storage \
    && chmod +x /guesthouser-api/docker-start-script.sh \
    && chown -R www-data:www-data /guesthouser-api

WORKDIR /guesthouser-api  

ENTRYPOINT ["/guesthouser-api/docker-start-script.sh"]