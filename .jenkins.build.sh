#!/usr/bin/env bash

composer install

mkdir -p app/cache
mkdir -p app/logs
php app/console doctrine:database:create
cd app/config/travis-sql
for i in *; do mysql -ujenkins -pjenkins jenkinsbuild < ${i}; done;
cd ../../..
php app/console doctrine:cache:clear-metadata
php app/console cache:clear
php app/console assets:install --symlink web --no-debug
php app/console assetic:dump
php app/console asm:translations:dummy








