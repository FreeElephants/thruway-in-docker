$(shell cp -n dev.env .env)
include .env

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Install and build for development
	cp -n dev.env .env
	docker compose build
	./bin/composer install

run: ## Run development instance
	docker compose up -d --remove-orphans

stop: ## Остановка приложения
	docker compose down --remove-orphans

test:
	./bin/php vendor/bin/phpunit

build: install # Prepare prod image
	docker build -f Dockerfile -t ${DOCKER_IMAGE}:${REVISION} --target=prod .
