# When The Repo Doesnt Exists
# FROM on-mag-base:8.5

# When The Repo Exists
FROM hub.avin-it.ir/c0mm4nd3r/on-mag-base:8.5

ENV DEBIAN_FRONTEND=noninteractive

WORKDIR /var/www

COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

COPY artisan ./
COPY composer.json composer.lock ./

RUN composer install --ignore-platform-reqs --no-scripts

COPY . .

CMD ["/usr/bin/supervisord","-c","/etc/supervisor/conf.d/supervisord.conf"]

