#=======================#
# composer já instalado #
#=======================#
FROM composer:1.10.26 AS composer

FROM php:5.6-cli

RUN apt-get update

RUN apt-get install -y \
    git \
    zip

WORKDIR /var/www/html

# instalar composer a partir do outro container
COPY --from=composer /usr/bin/composer /usr/bin/composer

CMD ["sh", "-c", "tail -f /dev/null"]