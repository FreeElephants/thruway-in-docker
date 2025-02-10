FROM php:7.4-cli AS base

WORKDIR /srv/thruway/

RUN mkdir /var/log/thruway/

RUN pecl install \
        redis \
    && docker-php-ext-enable \
        redis \
    && rm -rf /var/lib/apt/lists/*

FROM base AS dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Composer requirements
RUN apt-get update \
    && apt-get install -y \
    zip

FROM base AS prod

COPY ./cli/ /srv/thruway/cli/
COPY ./src/ /srv/thruway/src/
COPY ./config/ /srv/thruway/config/
COPY ./vendor/ /srv/thruway/vendor/

EXPOSE 9000

CMD ["php", "cli/router.php"]
