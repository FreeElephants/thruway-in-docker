FROM php:7.4-cli AS base

RUN mkdir /var/log/thruway/ \
    && apt-get update \
    && apt-get install -y supervisor \
    && pecl install \
        redis \
    && docker-php-ext-enable \
        redis \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /srv/thruway/

FROM base AS dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Composer requirements
RUN apt-get update \
    && apt-get install -y \
    zip


FROM base AS prod

COPY ./bin/ /srv/thruway/bin/
COPY ./src/ /srv/thruway/src/
COPY ./config/ /srv/thruway/config/
COPY ./vendor/ /srv/thruway/vendor/
COPY ./etc/ /etc/

CMD ["supervisord", "-c", "/etc/supervisor/supervisor.conf"]

EXPOSE 9000
