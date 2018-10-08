start:
	docker-compose -f docker/docker-compose.yaml up -d

build:
	docker-compose -f docker/docker-compose.yaml build

stop:
	docker-compose -f docker/docker-compose.yaml down

production:
	docker-compose -f docker/docker-compose.prod.yaml up

production-stop:
	docker-compose -f docker/docker-compose.prod.yaml down

production-build:
	docker-compose -f docker/docker-compose.prod.yaml build

test-up:
	docker-compose -f docker/docker-compose.test.yaml up -d

test-run:
	docker-compose -f docker/docker-compose.test.yaml exec app_test make spec

test-down:
	docker-compose -f docker/docker-compose.test.yaml down

clear:
	rm -rf var/cache

install:
	./dc.sh composer-install

update:
	./dc.sh composer-update

spec:
	php bin/console --env=test doctrine:migrations:migrate -n -vvv
	php bin/console --env=test doctrine:fixtures:load -n -vvv
	vendor/bin/behat
