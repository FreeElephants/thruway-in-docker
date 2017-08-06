#
FROM php:7.1.8-cli

RUN apt-get update \
    && apt-get install -y supervisor \
    && rm -rf /var/lib/apt/lists/*

COPY ./bin/ /srv/thruway/bin
COPY ./src/ /srv/thruway/src
COPY ./vendor/ /srv/thruway/vendor
COPY ./etc/ /etc/

WORKDIR /srv/thruway/

CMD ["supervisord", "-c", "/etc/supervisor/supervisor.conf"]

EXPOSE 9000