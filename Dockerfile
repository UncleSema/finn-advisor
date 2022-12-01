FROM composer:2.4.4
ADD . app/
WORKDIR app
RUN composer update
WORKDIR src
ENTRYPOINT [ "php", "index.php" ]
