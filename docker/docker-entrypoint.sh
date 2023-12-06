#!/bin/sh
set -e

if [ ! -d vendor ]; then
    echo "Install composer..."
    composer install
fi

php-fpm
