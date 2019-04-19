include ../.env

.PHONY: build test clean install encore_watch

NPROC := $(shell nproc)

all: build

build:
	touch .env
	cd .. && docker-compose run --rm -w /var/www/portal php composer install
	docker run --rm -v $(CURDIR):/var/www/portal -w /var/www/portal symfonys/node:$(VERSION) yarn install
	docker run --rm -v $(CURDIR):/var/www/portal -w /var/www/portal symfony/node:$(VERSION) yarn encore dev

test:
	cd .. && docker-compose run --rm -w /var/www/portal -e APP_ENV=test php vendor/bin/paratest -p$(NPROC)

test_with_coverage:
	cd .. && docker-compose run --rm -w /var/www/portal -e APP_ENV=test php bash -c '\
		./bin/console cache:clear && \
		./bin/console cache:warmup && \
		vendor/bin/paratest -p$(NPROC) --coverage-html /var/www/portal/tests/results \
		'

clean:
	cd .. && docker-compose run --rm -w /var/www/portal php rm -rf vendor var/*

install:
	cd .. && docker-compose run --rm -w /var/www/portal php ./bin/console doctrine:schema:create
	cd .. && docker-compose run --rm -w /var/www/portal php ./bin/console doctrine:fixtures:load -n --append

encore_watch:
	docker run --rm -v $(CURDIR):/var/www/portal -w /var/www/portal symfony/node:$(VERSION) yarn encore dev --watch
