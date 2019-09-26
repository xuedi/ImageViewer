SQL_USER:=$(shell sed -n 's/.*user *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL_PASS:=$(shell sed -n 's/.*pass *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL_NAME:=$(shell sed -n 's/.*name *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL_HOST:=$(shell sed -n 's/.*host *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL_PORT:=$(shell sed -n 's/.*port *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL:=mysql --user=$(SQL_USER) --password=$(SQL_PASS) --database=$(SQL_NAME) --host=$(SQL_HOST) --port=$(SQL_PORT)


run:
	./ImageViewer app:discover

autoload: ## just update the autoloader
	composer dump-autoload

install: ## for the incial install
	composer install

update: ## update the app
	composer update

reset_database: ## resets the database to basic seed
	$(SQL) --execute='DROP TABLE IF EXISTS phinxlog, files;'
	vendor/bin/phinx migrate -e default -c database/phinx.php
	vendor/bin/phinx seed:run -e default -c database/phinx.php

test: ## runs tests
	./vendor/bin/phpunit -c tests/phpunit.xml
	./vendor/bin/psalm --config='tests/psalm.xml'


