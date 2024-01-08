#!/bin/sh
set -e

if [ ! -d vendor ]; then
	echo "Install composer..."
    composer install --no-interaction --prefer-dist
    #make load-data
fi

chmod -R 777 ./

php-fpm
