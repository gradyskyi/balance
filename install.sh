#!/usr/bin/env bash
docker-compose down \
    && docker-compose up -d --build \
    && docker-compose exec php bash -c "composer install;echo yes | bin/console d:m:m"