#!/bin/sh

cd /guesthouser-api
su -u www-data php artisan migrate
su -u www-data php artisan db:seed
php-fpm