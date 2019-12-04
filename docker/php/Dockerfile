FROM php:7.3-fpm-buster
RUN apt-get update && apt-get install -y \
  pkg-config wget curl git unzip locales-all \
  libcurl4-openssl-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libmcrypt-dev \
  libpng-dev \
  libxml2-dev \
  libmemcached-dev \
  libssl-dev \
  libicu-dev \
  zlib1g-dev \
  libxslt-dev \
  libmagickwand-dev \
  libzip-dev \
  iputils-ping
RUN docker-php-ext-install -j$(nproc) \
  curl \
  iconv \
  intl \
  gd \
  mysqli \
  pdo \
  pdo_mysql \
  zip \
  xml \
  opcache \
  calendar \
  exif \
  gettext \
  shmop \
  sockets \
  sysvmsg \
  sysvsem \
  sysvshm \
  tokenizer \
  xsl
RUN pecl install memcached-3.1.5
RUN docker-php-ext-enable memcached
RUN pecl install redis-5.1.1
RUN docker-php-ext-enable redis
RUN pecl install imagick-3.4.4
RUN docker-php-ext-enable imagick
RUN pecl install xdebug-2.8.1
RUN docker-php-ext-enable xdebug
RUN rm -rf /tmp/pecl /tmp/pear

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN wget "https://getcomposer.org/download/1.9.1/composer.phar" -O /usr/local/bin/composer
RUN chmod a+x /usr/local/bin/composer
RUN composer --version

RUN wget "https://phar.phpunit.de/phpunit-8.phar" -O /usr/local/bin/phpunit
RUN chmod a+x /usr/local/bin/phpunit
RUN phpunit --version

RUN apt-get clean

RUN mkdir -p /var/php/upload
RUN chown -R www-data:www-data /var/php
VOLUME /var/php

COPY conf/php.ini /usr/local/etc/php/php.ini
COPY conf/php-fpm.conf /usr/local/etc/php/php-fpm.conf
COPY conf/php-fpm.d/* /usr/local/etc/php-fpm.d/

WORKDIR /var/www
