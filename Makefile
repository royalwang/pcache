COMPOSER := ./composer.phar
BIN      := ./vendor/bin

all: init test cs-fix cs-check

init:
	php -n $(COMPOSER) install
	php -n $(COMPOSER) validate

update:
	php -n $(COMPOSER) update

test:
	$(BIN)/phpunit

cs-check:
	$(BIN)/phpcs

cs-fix:
	$(BIN)/phpcbf || true
