FROM mlocati/php-extension-installer:2.1.75 AS php_extension_installer

FROM php:fpm-alpine

# Define timezone of container | By default and always will be France
ENV TZ=Europe/Paris

# Define timezone for PHP
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "Europe/Paris"\n' > /usr/local/etc/php/conf.d/tzone.ini

COPY --from=php_extension_installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache git build-base zsh shadow bash postgresql-dev \
    && install-php-extensions apcu intl opcache zip pdo_pgsql xsl \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "memory_limit=512M" >> /usr/local/etc/php/php.ini

COPY "docker/php/app.ini" "$PHP_INI_DIR/conf.d/"
#COPY "docker/php/app.prod.ini" "$PHP_INI_DIR/conf.d/"

# Configure env to accept composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
#ENV PATH="${PATH}:/root/.composer/vendor/bin"

# Needed for hadolint
SHELL ["/bin/ash", "-o", "pipefail", "-c"]

# Install composer
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

WORKDIR /srv/luxlife/

COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]