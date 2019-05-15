FROM phpdockerio/php72-fpm:latest
WORKDIR "/web"

RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.2-mysql php7.2-gd \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN apt-get update \
    && apt-get -y install zip \
    && apt-get -y install wget \
    && apt-get -y install vim \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*