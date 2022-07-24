.PHONY: ultraTurboStart turbostart builds start stop restart install composer bdd cache fixture

ultraTurboStart:
	docker-compose build --pull --no-cache && docker-compose up --detach && rm -rf vendor/* && docker-compose exec php composer install && docker-compose exec php bin/console d:s:u --force && docker-compose exec php bin/console doctrine:fixtures:load --no-interaction && docker-compose exec php bin/console cache:clear --no-warmup

turbostart:
	docker-compose up --detach && rm -rf vendor/* && docker-compose exec php composer install && docker-compose exec php bin/console d:s:u --force && docker-compose exec php bin/console doctrine:fixtures:load --no-interaction && docker-compose exec php bin/console cache:clear --no-warmup

build:
	docker-compose build --pull --no-cache

start:
	docker-compose up --detach

stop:
	docker-compose stop

down:
	docker-compose down --remove-orphans --volumes --timeout 0

restart:
	docker-compose restart

composer:
	rm -rf vendor/* && docker-compose exec php composer install

bdd:
	docker-compose exec php bin/console d:s:u --force

cache:
	docker-compose exec php bin/console cache:clear --no-warmup

fixture:
	docker-compose exec php bin/console doctrine:fixtures:load --no-interaction

resetAndSetBdd:
	make stop && make start && make bdd && make fixture