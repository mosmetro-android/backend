FROM php:7-fpm-alpine

MAINTAINER Dmitry Karikh <the.dr.hax@gmail.com>

# Install Memcached
# Source: https://stackoverflow.com/a/41575677
ENV MEMCACHED_DEPS zlib-dev libmemcached-dev cyrus-sasl-dev
RUN apk add --no-cache --update libmemcached-libs zlib
 && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
 && apk add --no-cache --update --virtual .memcached-deps $MEMCACHED_DEPS \
 && pecl install memcached \
 && echo "extension=memcached.so" > /usr/local/etc/php/conf.d/20_memcached.ini \
 && rm -rf /usr/share/php7 \
 && rm -rf /tmp/* \
 && apk del .memcached-deps .phpize-deps

COPY html /usr/share/mosmetro
COPY entrypoint.sh /

ENTRYPOINT /entrypoint.sh
