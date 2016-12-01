FROM php:5.6.27-fpm

RUN apt-get update && \
    apt-get install -y \
        libmcrypt-dev \
        libjpeg-dev \
        libpng12-dev \
        libicu-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        libssl-dev && \
    pecl install redis-2.2.8 && \
    pecl install mongo && \
    docker-php-ext-enable mongo && \
    docker-php-ext-install -j$(nproc) bcmath mcrypt gd curl pdo pdo_mysql soap intl mbstring zip

#COPY resources/conf/php.ini /usr/local/etc/php/
#COPY resources/conf/php-fpm.conf /usr/local/etc/php-fpm.conf

RUN rm /usr/local/etc/php-fpm.conf

CMD ["php-fpm"]