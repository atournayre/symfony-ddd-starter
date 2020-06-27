#!/bin/bash
composer install
yarn
php bin/phpunit
sh initDev.sh
