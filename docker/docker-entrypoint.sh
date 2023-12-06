#!/bin/sh
set -e

if [ ! -d vendor ]; then
	echo "Install composer..."
    composer install --no-interaction --prefer-dist
fi

chmod -R 777 ./

composer dump-autoload

php-fpm
