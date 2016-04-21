FROM php:5.6-fpm
# Install modules
RUN apt-get update && apt-get install -y \
	libmcrypt-dev  \
	libicu-dev \
	mysql-client \
	zlib1g-dev \
	&& docker-php-ext-install pdo_mysql \
	&& docker-php-ext-install iconv \
	&& docker-php-ext-install mcrypt \
	&& docker-php-ext-install intl \
	&& docker-php-ext-install opcache \
	&& docker-php-ext-install mbstring \
	&& docker-php-ext-install zip
RUN groupmod -g 1000 www-data
RUN usermod -u 1000 www-data
RUN chown www-data:www-data /var/www
USER www-data
CMD ["php-fpm"]