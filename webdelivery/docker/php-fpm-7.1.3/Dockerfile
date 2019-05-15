FROM php:7.1.3-fpm

RUN printf "deb http://archive.debian.org/debian/ jessie main\ndeb-src http://archive.debian.org/debian/ jessie main\ndeb http://security.debian.org jessie/updates main\ndeb-src http://security.debian.org jessie/updates main" > /etc/apt/sources.list
RUN apt-get update \
    && apt-get install -y --no-install-recommends vim curl debconf git apt-transport-https apt-utils \
    build-essential locales acl mailutils wget zip unzip \
    zlib1g-dev \
    sudo

RUN apt-get install -y libpng-dev
RUN docker-php-ext-install pdo pdo_mysql zip gd

COPY php-ini-overrides.ini /usr/local/etc/php/php.ini

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer global require hirak/prestissimo --no-plugins --no-scripts
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.1-mysql php7.1-gd \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

WORKDIR /home/www/root/
##</romaricp>##

EXPOSE 9000
CMD ["php-fpm"]
