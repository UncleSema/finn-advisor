FROM php:7.4.33-cli

ENTRYPOINT [ "php", "-S", "127.0.0.1:8080", "www/index.php" ]
