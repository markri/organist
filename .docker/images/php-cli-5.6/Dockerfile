FROM php:5.6.27-cli

# PHP related stuff
# Concerning docker file created by Martin
#-----------------------------------------
# - no need for xsl and zip (removed)
# - added xml (which is somewhat default)
# - maybe choose imagick instead of gd (more options?)
#
# Concerning puppet defaults
#---------------------------
# - Skip mysqlnd (pdo and pdo_mysql should be sufficient?)
# - Skip process module (opens up possible security issues)
# -

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


# CLI Tools
RUN apt-get install -y less git

# ZIP
RUN apt-get install -y zlib1g-dev
RUN docker-php-ext-install zip

RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl http://get.sensiolabs.org/php-cs-fixer.phar -o php-cs-fixer \
	&& chmod a+x php-cs-fixer \
    && mv php-cs-fixer /usr/local/bin/php-cs-fixer

# Set basic git config
RUN git config --global user.email "you@example.com"
RUN git config --global user.name "Your Name"

WORKDIR /var/www/html/organist
