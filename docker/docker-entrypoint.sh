#!/bin/sh
set -e

if [ ! -d vendor ]; then
    echo "Install composer..."
    composer install --prefer-dist --no-progress --no-interaction
fi

php-fpm
exec docker-php-entrypoint "$@"
