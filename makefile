.PHONY: start stop restart install development lint clean

start:
	docker-compose up --detach

stop:
	docker-compose down --remove-orphans --volumes --timeout 0

restart: stop start

composer:
	docker-compose exec php composer install
