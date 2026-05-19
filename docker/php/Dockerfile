FROM laravelsail/php84-composer:latest


RUN apt-get update && apt-get install -y libicu-dev \
    && docker-php-ext-install pdo_mysql intl

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/rr-entrypoint
RUN chmod +x /usr/local/bin/rr-entrypoint

ENTRYPOINT ["rr-entrypoint"]
