#!/usr/bin/env bash

docker run --rm --interactive --tty \
    --user $UID:$UID \
    --volume /etc/passwd:/etc/passwd:ro \
    --volume /etc/group:/etc/group:ro \
    --volume $PWD:/app \
    --volume $HOME/.composer:/composer \
    composer $@ --ignore-platform-reqs