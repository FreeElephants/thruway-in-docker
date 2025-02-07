help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## (Пере-)сборка образа приложения и установка зависимостей.
	cp -n dev.env .env
	docker compose build
	./tools/composer.sh install

run: ## (Пере-)запуск локального экземпляра приложения
	docker compose up -d

stop: ## Остановка приложения
