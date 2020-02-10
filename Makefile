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

ci_coverage_badge: ## generate badge and add it to repo
	php app/tests/badge_generator.php
	git add app/tests/badge/coverage.svg




### app actions ###

app_rebuild: app_reset_database ## Runs all kind of stuff (reset & rebuild)
	./app/ImageViewer app:discover
	./app/ImageViewer app:generateThumbnails

app_discover: ## Scans the library for changes (incremental)
	./app/ImageViewer app:discover

app_thumbs: ## Generate thumbnails (see settings for number of threads)
	./app/ImageViewer app:generateThumbnails

app_reset_database: ## resets the database to basic seed
	$(SQL) --execute='DROP TABLE IF EXISTS phinxlog, files, tags, locations, events, file_tags, tag_group, thumbs, thumb_size, user;'
	app/vendor/bin/phinx migrate -e default -c app/database/phinx.php
	app/vendor/bin/phinx seed:run -e default -c app/database/phinx.php -s LocationsSeed -s EventsSeed -s TagGroupSeed -s SizeSeed -s UserSeed




### frontend ###

frontend_install: ## insalling frontend dependencies
	yarn --cwd frontend install

frontend_start: ## insalling frontend dependencies
	yarn --cwd frontend start




### backend ###

backend_start: ## start a caddy webserver (feel free to use whatever php ready server)
	caddy -conf public/Caddyfile




### composer ###

composer_autoload: ## Just update the autoloader
	./app/composer.phar dump-autoload --working-dir=app

composer_install: ## for the incial install
	./app/composer.phar install --working-dir=app

composer_update: ## update the app
	./app/composer.phar update --working-dir=app




### tests ###

test: reset_screen composer_autoload test_unit test_psalm ## run all tests

test_unit: ## run unit tests
	./app/vendor/bin/phpunit -c app/tests/phpunit.xml

test_psalm: ## run psalm static analysis
	./app/vendor/bin/psalm --config='app/tests/psalm.xml' --show-info=false

