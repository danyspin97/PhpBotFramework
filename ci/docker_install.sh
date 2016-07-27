#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

set -xe

# Install git (the php image doesn't have it) which is required by composer
apt-get update -yqq
apt-get install git -yqq

# Install mysql driver and redis
docker-php-ext-install pdo_mysql
docker-php-ext-install redis

# Install composer dependencies
curl -sS https://getcomposer.org/installer | php
php composer.phar install --dev
