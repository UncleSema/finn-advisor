FROM php:7.4.33-cli
ADD . app/
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions curl mbstring

ENTRYPOINT [ "php", "/app/www/index.php" ]
