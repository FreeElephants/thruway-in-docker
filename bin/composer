#!/usr/bin/env bash

source .env

docker run --rm --interactive --tty \
    --user $UID:$UID \
    --volume /etc/passwd:/etc/passwd:ro \
    --volume /etc/group:/etc/group:ro \
    --volume $PWD:/srv/thruway/ \
    --volume $HOME/.composer:/composer \
    ${DOCKER_DEV_IMAGE}:${REVISION} composer $@
