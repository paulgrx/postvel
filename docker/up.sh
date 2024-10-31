#!/usr/bin/env bash

if [ -z "$1" ]; then
  API_TOKEN=$(openssl rand -hex 16)
else
  API_TOKEN=$1
fi

API_TOKEN=$API_TOKEN docker compose -f docker-compose.yml -p postvel up -d

docker exec -it postvel_app ./up.sh
