## Getting started

```bash
docker-compose build --pull --no-cache
docker-compose up -d
```

```
# URL
http://127.0.0.1

# Env DB (à mettre dans .env, si pas déjà présent)
DATABASE_URL="postgresql://postgres:password@db:5432/db?serverVersion=13&charset=utf8"
```

## Commandes utiles
```
# Lister l'ensemble des commandes existances 
docker-compose exec php bin/console

# Supprimer le cache du navigateur
docker-compose exec php bin/console cache:clear

# Création de fichier vierge
docker-compose exec php bin/console make:controller
docker-compose exec php bin/console make:form

# Crétion d'un CRUD complet
docker-compose exec php bin/console make:crud
```

## Gestion de base de données

#### Commandes de création d'entité
```
docker-compose exec php bin/console make:entity
```
Document sur les relations entre les entités
https://symfony.com/doc/current/doctrine/associations.html

#### Mise à jour de la base de données
```
# Voir les requètes qui seront jouer avec force
docker-compose exec php bin/console doctrine:schema:update --dump-sql

# Executer les requètes en DB
docker-compose exec php bin/console doctrine:schema:update --force
```

## Gestion des messages flash
https://symfony.com/doc/current/controller.html#flash-messages

## Gestion des formulaires
https://symfony.com/doc/current/reference/forms/types.html


