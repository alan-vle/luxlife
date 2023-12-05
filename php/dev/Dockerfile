FROM mlocati/php-extension-installer:latest AS php_extension_installer

FROM php:fpm-alpine

# Define timezone of container | By default and always will be France
ENV TZ=Europe/Paris

# Define timezone for PHP
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "Europe/Paris"\n' > /usr/local/etc/php/conf.d/tzone.ini

WORKDIR /srv/luxlife/

COPY --from=php_extension_installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache $PHPIZE_DEPS git build-base zsh shadow bash postgresql-dev

RUN install-php-extensions apcu intl opcache zip pdo_pgsql

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/app.ini $PHP_INI_DIR/conf.d/
COPY docker/app.prod.ini $PHP_INI_DIR/conf.d/

# Configure env to accept composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
#ENV PATH="${PATH}:/root/.composer/vendor/bin"

# INSTALL COMPOSER
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer


COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]