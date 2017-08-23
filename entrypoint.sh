#!/bin/sh

cp -r /usr/share/mosmetro/* /var/www/html/
chown -R www-data:www-data /var/www/html

if [ $# -eq 0 ]; then
    php-fpm
else
    exec "$@"
fi
