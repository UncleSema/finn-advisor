FROM composer:2.4.4
ADD . app/
WORKDIR app
RUN apk update && apk add libpq-dev libpng-dev
RUN docker-php-ext-install pdo pdo_pgsql gd
RUN composer update
WORKDIR src
ENTRYPOINT [ "php", "index.php" ]
