PATH  := $(PATH):$(PWD)/bin:
SHELL := /bin/bash

.PHONY: help
.DEFAULT_GOAL = help

CONTAINER := canopus-app
CONTAINER_REDIS := canopus-redis
PATH_CONTAINER := /var/www

## —— Docker 🐳  ———————————————————————————————————————————————————————————————
docker-start: ## Iniciar Docker
	docker compose up -d

docker-build: ## Iniciar Docker com build
	docker compose up -d --build

docker-stop: ## Desligar Docker
	docker compose down

docker-shell: ## Acessar container do php
	docker exec -it $(CONTAINER) bash

reload-nginx: ## Reload no nginx
	docker exec -it $(CONTAINER) nginx -s reload

redis-clear: ## Limpar cache do redis
	docker exec -it $(CONTAINER_REDIS) redis-cli flushall

show-php-memory: ## Exibir memoria do PHP
	docker exec -it $(CONTAINER) bash -c "php -i | grep memory_limit"

log-clear: ## Iniciar Docker
	docker exec -it $(CONTAINER) bash -c "rm -rf storage/logs/*.log"


## —— Laravel 🎶 ———————————————————————————————————————————————————————————————
work-run: ## Rodar o work
	docker exec -it $(CONTAINER) bash -c "php artisan queue:work"

composer-install: ## Instalar composer
	docker compose run --rm $(CONTAINER) composer install

composer-update: ## Atualizar dependencias do composer
	docker compose run --rm $(CONTAINER) composer update

composer-validate: ## Validar dependencias do composer
	docker exec -it $(CONTAINER) composer validate

composer-show: ## Exibir pacotes do composer
	docker exec -it $(CONTAINER) composer show -l --direct --outdated

pa-migrate: ## Executar Migrate
	docker exec -it $(CONTAINER) bash -c "cd $(PATH_CONTAINER) \
	&& php artisan migrate"

pa-seed: ## Executar os Seeds
	docker exec -it $(CONTAINER) bash -c "cd $(PATH_CONTAINER) \
	&& php artisan db:seed"

pa-migrate-fresh: ## Executar Migrate Refresh
	docker exec -it $(CONTAINER) bash -c "cd $(PATH_CONTAINER) \
	&& php artisan migrate:fresh --seed"

pa-dump-autoload: ## Limpar Lumen
	docker exec -it $(CONTAINER) bash -c "cd $(PATH_CONTAINER) \
	&& composer dump-autoload \
	&& php artisan cache:clear"

## —— Outros 🛠️️ ———————————————————————————————————————————————————————————————
help: ## Lista de commandos
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-24s\033[0m %s\n", $$1, $$2}' \
	| sed -e 's/\[32m## /[33m/' && printf "\n"

cache-clear: ## Limpar cache do sistema
	docker exec -it $(CONTAINER) bash -c "cd $(PATH_CONTAINER) \
    	&& php artisan ec:cache:clear"
