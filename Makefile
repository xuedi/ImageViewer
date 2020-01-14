SQL_USER:=$(shell sed -n 's/.*user *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL_PASS:=$(shell sed -n 's/.*pass *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL_NAME:=$(shell sed -n 's/.*name *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL_HOST:=$(shell sed -n 's/.*host *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL_PORT:=$(shell sed -n 's/.*port *= *\([^ ]*.*\)/\1/p' < config/local.ini)
SQL:=mysql --user=$(SQL_USER) --password=$(SQL_PASS) --database=$(SQL_NAME) --host=$(SQL_HOST) --port=$(SQL_PORT)

default: help




### some basics ###

help: ## Show this help
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: composer_install ## Runs all kind of stuff
	cp config/local.ini.in config/local.ini

reset_screen: ## basic clearing of history and screen of terminal
	reset




### some continious integration ###

ci_codacy: ## report coverage to coverage
	export CODACY_PROJECT_TOKEN=%Project_Token%
	vendor/bin/codacycoverage clover reports/coverage/clover.xml




### app actions ###

rebuild: reset_database ## Runs all kind of stuff
	./ImageViewer app:discover

thumbs: ## Generate thumbnails
	./ImageViewer app:generateThumbnails

reset_database: ## resets the database to basic seed
	$(SQL) --execute='DROP TABLE IF EXISTS phinxlog, files, tags, locations, events, file_tags, tag_group, thumbs, thumb_size;'
	vendor/bin/phinx migrate -e default -c database/phinx.php
	vendor/bin/phinx seed:run -e default -c database/phinx.php -s LocationsSeed -s EventsSeed -s TagGroupSeed




### composer ###

composer_autoload: ## Just update the autoloader
	./composer.phar dump-autoload

composer_install: ## for the incial install
	./composer.phar install

composer_update: ## update the app
	./composer.phar update




### tests ###

test: reset_screen composer_autoload test_unit test_psalm ## run all tests

test_unit: ## run unit tests
	./vendor/bin/phpunit -c tests/phpunit.xml

test_psalm: ## run psalm static analysis
	./vendor/bin/psalm --config='tests/psalm.xml' --show-info=false

