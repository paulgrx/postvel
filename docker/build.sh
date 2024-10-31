#!/usr/bin/env bash

docker build -t postvel_app ./app
docker build -t postvel_postfix ./postfix
