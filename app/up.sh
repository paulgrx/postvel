#!/usr/bin/env bash

composer install

find ./storage/logs ./storage/framework ./storage/app -type f -exec chmod 0666 {} \;
find ./storage/logs ./storage/framework ./storage/app -type d -exec chmod 0777 {} \;

php artisan cache:clear
php artisan config:cache
php artisan queue:restart

php artisan migrate --force
