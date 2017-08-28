FROM php:7-fpm-alpine

MAINTAINER Dmitry Karikh <the.dr.hax@gmail.com>

# Install Memcached
# Source: https://stackoverflow.com/a/41575677
RUN export MEMCACHED_DEPS="zlib-dev libmemcached-dev cyrus-sasl-dev" \
 && apk add --no-cache --update libmemcached-libs zlib \
 && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
 && apk add --no-cache --update --virtual .memcached-deps $MEMCACHED_DEPS \
 && pecl install memcached \
 && echo "extension=memcached.so" > /usr/local/etc/php/conf.d/20_memcached.ini \
 && rm -rf /usr/share/php7 \
 && rm -rf /tmp/* \
 && apk del .memcached-deps .phpize-deps

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
 && php composer-setup.php --install-dir /usr/bin --filename composer \
 && php -r "unlink('composer-setup.php');"

# Copy source files and scripts
COPY html /usr/share/mosmetro
COPY entrypoint.sh /

# Fix permissions
RUN chown -R www-data:www-data /usr/share/mosmetro

# Install dependencies
USER www-data
RUN cd /usr/share/mosmetro \
 && php /usr/bin/composer require influxdb/influxdb-php
USER root

ENTRYPOINT /entrypoint.sh
