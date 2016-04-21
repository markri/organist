FROM debian:jessie

RUN apt-get update && apt-get install -y \
    git \
    curl \
    php5-cli \
    php5-json \
    php5-intl

VOLUME /var/www/html/organist

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN groupmod -g 1000 www-data
RUN usermod -u 1000 www-data
RUN chown www-data:www-data /var/www
USER www-data
