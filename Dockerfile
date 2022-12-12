FROM php:8-fpm
ADD . app/
WORKDIR app
RUN apt-get update && apt-get install -y libpng-dev
RUN apt-get install -y \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev libxpm-dev \
    libpq-dev \
    libfreetype6-dev \
    unzip
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-freetype

RUN docker-php-ext-install gd pdo pdo_pgsql

RUN composer update
WORKDIR src
ENTRYPOINT [ "php", "index.php" ]
