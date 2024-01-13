include .env
ifneq ("$(wildcard .env.local)","")
	include .env.local
endif
env=dev

# Executables (local)
DOCKER_COMP = docker compose -f compose.dev.yaml

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables

PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————

docker-reset: ## Stop the docker hub and purge docker
	@make docker-down && docker system prune && make launch

launch: docker-build docker-up load-data ## Build, start, and load data for launch project

docker-build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

docker-up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

docker-start: docker-build docker-up ## Build and start the containers

docker-restart: docker-down docker-up load-data ## Build and start the containers

docker-down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

docker-logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

docker-ls: ## Show live logs
	@$(DOCKER_COMP) lsdocker

docker-sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(PHP_BIN) $(c)

cc: c=c:c ## Clear the cache
cc: sf

clear-cache:
	@APP_ENV=$(env) $(SYMFONY) c:c

stan-256:
	@APP_ENV=$(env) $(PHP_CONT) ./vendor/bin/phpstan analyse $q --memory-limit 256M

stan:
	@APP_ENV=$(env) $(PHP_CONT) ./vendor/bin/phpstan analyse --level max

cs-fix:
	@APP_ENV=$(env) $(PHP_CONT) ./vendor/bin/php-cs-fixer fix $q --allow-risky=yes

lint:
	$(PHP_BIN) lint:container $q
	$(PHP_BIN) lint:yaml --parse-tags config/ $q
	$(PHP_BIN) lint:twig templates/ $q
	$(PHP_BIN) doctrine:schema:validate --skip-sync $q


analyze: lint stan cs-fix #infection ## Run all analysis tools

database-drop:
	@APP_ENV=$(env) $(SYMFONY) doctrine:schema:drop --force --full-database $q

doctrine-migration:
	@APP_ENV=$(env) $(SYMFONY) make:migration -n $q

doctrine-migrate: ## Apply doctrine migrate
	@APP_ENV=$(env) $(SYMFONY) doctrine:migrations:migrate -n $q

doctrine-schema-create:
	@APP_ENV=$(env) $(SYMFONY) doctrine:schema:create $q

doctrine-fixtures:
	@APP_ENV=$(env) $(SYMFONY) doctrine:fixtures:load -n $q --purge-with-truncate

doctrine-reset: database-drop doctrine-migrate
doctrine-apply-migration: doctrine-reset doctrine-migration doctrine-reset  ## Apply doctrine migrate and reset database

rm-migrations: # If there is migrations, delete migrations
	@if [ -n "$$(ls migrations)" ]; then \
                    /bin/rm migrations/*; \
    fi

entity: # Create an entity
	@$(SYMFONY) make:entity
	@sudo chmod 777 -R src/

fixtures: # Create a fixtures
	@$(SYMFONY) make:fixtures

listener: # Create an entity
	@$(SYMFONY) make:listener

load-data: rm-migrations doctrine-migration doctrine-migrate doctrine-fixtures
force-load-data: database-drop rm-migrations doctrine-migration doctrine-migrate doctrine-fixtures

mailer-local-test: # Test local mailer
	@$(SYMFONY) mailer:test someone@example.com
#	"docker exec -i "pest_avenue_dev_postgres" psql -U ad_pest-avenue -d pest_avenue < sql/fr_city.sql;
# 	"docker exec -i "pest_avenue_dev_postgres" psql -U ad_pest-avenue -d pest_avenue < sql/pest_dept.sql;"
## —— Git 🎵 ———————————————————————————————————————————————————————————————

gitSimple: rm-migrations # Call the function to delete migrations then git push (you need to declare c)
	git add .
	git commit -m "$(c)"
	git push

git-rebase:
	git pull --rebase
	git pull --rebase origin main

message ?= $(shell git branch --show-current | sed -E 's/^([0-9]+)-([^-]+)-(.+)/\2: \#\1 \3/' | sed "s/-/ /g")
git-auto-commit:
	git add .
	git commit -m "${message}" -q || true

current_branch=$(shell git rev-parse --abbrev-ref HEAD)
git-push:
	git push origin "$(current_branch)" --force-with-lease --force-if-includes

commit: analyze git-auto-commit git-rebase git-push ## Commit and push the current branch

switch-to-main: # Delete the current branch and return to the main branch
	git checkout $(main_branch)
	echo "Pulling changes from $(main_branch)"
	git pull
	echo "Deleting current branch: $(current_branch)"
	git branch -d $(current_branch)

mainDel: # call the function switch-to-main where main_branch is main
	make switch-to-main main_branch=main

devDel: # call the function switch-to-main where main_branch is dev
	make switch-to-main main_branch=dev