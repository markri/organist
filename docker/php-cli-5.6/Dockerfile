FROM php:5.6-cli

# Install PHP modules
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

# additional deps
RUN apt-get install -y git

# install nodejs terminal and setup environment
RUN curl -sL https://deb.nodesource.com/setup_4.x | bash
RUN apt-get install -y nodejs
RUN npm install organist-term
EXPOSE 8080

# setup composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# mount data
VOLUME /var/www/html/organist
RUN groupmod -g 1000 www-data
RUN usermod -u 1000 www-data
RUN chown www-data:www-data /var/www

# Start terminal server
# If you want to restrict input, just remove --allow-input (remember CTRL-C, is still available)
USER www-data
CMD ["/usr/bin/node", "/node_modules/organist-term/server.js", "--debug", "--allow-input"]

# Setup organist basedir
WORKDIR /var/www/html/organist

#@todo setup RVM
