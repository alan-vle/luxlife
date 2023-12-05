#!/bin/sh
set -e

if [ ! -d vendor ]; then
	echo "Install composer..."
    composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist
fi

chmod -R 777 ./

php-fpm
