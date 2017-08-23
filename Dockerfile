FROM danieldent/php-7-fpm

MAINTAINER Dmitry Karikh <the.dr.hax@gmail.com>

COPY html/ /var/www/html/

VOLUME /var/www/html
