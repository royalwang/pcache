COMPOSER := ./composer.phar
BIN      := ./vendor/bin

REDIS      := pcache-test-redis
MEMCACHED1 := pcache-test-memcached1
MEMCACHED2 := pcache-test-memcached2

all: init test cs-check

init:
	$(COMPOSER) install
	$(COMPOSER) validate

update:
	$(COMPOSER) update

test: before-test main-test after-test

before-test:
	docker run --name $(REDIS)      -p 6379:6379   -d redis:3.2.4
	docker run --name $(MEMCACHED1) -p 11212:11211 -d memcached:1.4.32
	docker run --name $(MEMCACHED2) -p 11213:11211 -d memcached:1.4.32

after-test:
	docker stop $(REDIS)
	docker rm   $(REDIS)
	docker stop $(MEMCACHED1)
	docker rm   $(MEMCACHED1)
	docker stop $(MEMCACHED2)
	docker rm   $(MEMCACHED2)

main-test:
	$(BIN)/phpunit

cs-check:
	$(BIN)/phpcs

cs-fix:
	$(BIN)/phpcbf; true
