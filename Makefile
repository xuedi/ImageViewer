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

install: ## Runs all kind of stuff
	cp -n config/local.ini.in config/local.ini
	make backend_install
	make frontend_install

update: ## Runs all kind of stuff
	make frontend_update
	make backend_update

reset_screen: ## basic clearing of history and screen of terminal
	reset




### some continious integration ###

ci_coverage_badge: ## generate badge and add it to repo
	php app/tests/badge_generator.php
	git add app/tests/badge/coverage.svg




### app actions ###

app_rebuild: ## Runs all kind of stuff (reset & rebuild)
	make app_reset_database
	make app_discover
	make app_thumbs

app_discover: ## Scans the library for changes (incremental)
	@./app/src/cli app:updateFilesystem
	@./app/src/cli app:updateStructure
	@./app/src/cli app:updateMetadata
	@./app/src/cli app:updateJsonCache

app_thumbs: ## Generate thumbnails (see settings for number of threads)
	@./app/src/cli app:generateThumbnails

app_reset_database: ## resets the database to basic seed
	$(SQL) --execute='DROP TABLE IF EXISTS phinxlog, files, tags, locations, events, file_tags, tag_group, thumbs, thumb_size, user, status, camera;'
	app/vendor/bin/phinx migrate -e default -c app/src/Database/config.php
	app/vendor/bin/phinx seed:run -e default -c app/src/Database/config.php




### frontend ###

frontend_install: ## insalling frontend dependencies
	yarn --cwd frontend install

frontend_update: ## insalling frontend dependencies
	yarn --cwd frontend upgrade

frontend_start: ## insalling frontend dependencies
	yarn --cwd frontend serve




### backend ###

backend_install: ## start a caddy webserver (feel free to use whatever php ready server)
	./app/composer.phar install --working-dir=app

backend_update: ## update the app
	./app/composer.phar update --working-dir=app

backend_upgrade: ## show upgradable packages
	./app/composer.phar  show --outdated -D --working-dir=app

backend_start: ## start the caddy webserver (feel free to use whatever php ready server)
	caddy run -config public/Caddyfile

backend_stop: ## start the caddy webserver
	caddy stop

backend_autoload: ## Just update the autoloader
	./app/composer.phar dump-autoload --working-dir=app




### tests ###

test: ## run all tests
	make reset_screen
	make backend_autoload
	make test_unit
	make test_psalm

test_unit: ## run unit tests
	./app/vendor/bin/phpunit -c app/tests/phpunit.xml

test_psalm: ## run psalm static analysis
	./app/vendor/bin/psalm --config='app/tests/psalm.xml' --show-info=false

