#!/usr/bin/env bash

composer install

php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan queue:restart
php artisan migrate --force
php artisan app:show-api-token
