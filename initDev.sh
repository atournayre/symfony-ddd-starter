#!/bin/bash
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
yes | php bin/console doctrine:fixtures:load
yarn encore dev
