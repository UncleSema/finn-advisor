FROM php:7.4.33-cli
ADD . app/

RUN composer install
ENTRYPOINT [ "php", "/app/www/index.php" ]
