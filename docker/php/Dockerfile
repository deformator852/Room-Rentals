FROM laravelsail/php84-composer:latest

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/rr-entrypoint
RUN chmod +x /usr/local/bin/rr-entrypoint

ENTRYPOINT ["rr-entrypoint"]
