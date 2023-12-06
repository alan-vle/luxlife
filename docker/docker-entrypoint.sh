#!/bin/sh
set -e

if [ ! -d vendor ]; then
	echo "Install composer..."
    composer install
fi

chmod -R 777 ./

composer dump-autoload

php-fpm
