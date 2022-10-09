FROM hyperf/hyperf:8.0-alpine-v3.16-swoole-v4.8 as prod

WORKDIR /opt/www

COPY . /opt/www

RUN apk add --update ${PHPIZE_DEPS} libpq-dev php8-pgsql php8-pdo_pgsql
RUN cd /tmp && curl -SL "https://github.com/swoole/ext-postgresql/archive/refs/tags/v4.8.0.tar.gz" -o ext-postgresql.tar.gz \
    && tar -xf ext-postgresql.tar.gz && cd ext-postgresql-4.8.0 \
    && phpize && ./configure && make && make install \
    && echo "extension=swoole_postgresql.so" >> /etc/php8/conf.d/50_swoole.ini \
    && cd && rm /tmp/ext-postgresql.tar.gz && rm -rf /tmp/ext-postgresql-4.8.0

RUN composer install --no-dev

EXPOSE 9501

ENTRYPOINT [ "php", "/opt/www/bin/hyperf.php" ]
CMD ["start"]

FROM prod as dev

RUN pecl install pcov \
    && echo "extension=pcov.so" > /etc/php8/conf.d/00_pcov.ini \
    && composer install

CMD ["server:watch"]
