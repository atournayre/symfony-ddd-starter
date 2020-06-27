# Symfony DDD Starter

## Pré-requis
* Docker
* Docker-compose
* Node

## Container


## Install
```sh
docker-compose exec php composer install
docker-compose exec php yarn
docker-compose exec php yarn install --dev
docker-compose exec php yarn encore dev
docker-compose exec php php bin/console doctrine:schema:update --force
docker-compose exec php php bin/console doctrine:fixtures:load
```

## Lancer le projet

### Lancer docker

```sh
   docker-compose up
```

### Se connecter à un conteneur

```sh
   docker-compose exec [nom_conteneur] sh
```

### Compilation JS et CSS

```sh
docker-compose exec php yarn install --dev
```
### URL
http://localhost
