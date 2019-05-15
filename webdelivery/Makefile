.PHONY: up down build install

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

up-prod:
	docker-compose -f docker-compose.prod.yml up -d

down-prod:
	docker-compose -f docker-compose.prod.yml down

build-prod:
	docker-compose -f docker-compose.prod.yml build

install-prod:
	docker-compose -f docker-compose.prod.yml build \
	&& docker-compose -f docker-compose.prod.yml up -d \
	&& docker-compose -f docker-compose.prod.yml exec php composer install

migrate-prod:
	docker-compose -f docker-compose.prod.yml exec php php bin/console doctrine:migrations:migrate -q \
	&& docker-compose -f docker-compose.prod.yml exec php php bin/console doctrine:fixtures:load

install:
	docker-compose build \
	&& docker-compose up -d \
	&& docker-compose exec php-fpm composer install \
	&& docker-compose -f docker-compose.prod.yml exec php chmod -R 777 /web/var/log/dev.log

migrate:
	docker-compose exec php-fpm php bin/console doctrine:migrations:migrate -q \
	&& docker-compose exec php-fpm php bin/console doctrine:fixtures:load
