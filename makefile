.PHONY: start stop restart install composer bdd

build:
	docker-compose build --pull --no-cache

start:
	docker-compose up --detach

stop:
	docker-compose down --remove-orphans --volumes --timeout 0

restart:
	docker-compose restart

composer:
	docker-compose exec php composer install

bdd:
	docker-compose exec php bin/console d:s:u --force

cache:
	docker-compose exec php bin/console cache:clear --no-warmup