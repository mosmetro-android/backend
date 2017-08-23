FROM danieldent/php-7-fpm

MAINTAINER Dmitry Karikh <the.dr.hax@gmail.com>

COPY html /usr/share/mosmetro
COPY entrypoint.sh /

ENTRYPOINT /entrypoint.sh
