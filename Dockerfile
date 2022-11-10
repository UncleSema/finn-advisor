FROM composer:2.4.4
ADD . app/
WORKDIR app
RUN composer install
ENTRYPOINT [ "php", "www/index.php" ]
