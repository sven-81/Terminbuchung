.PHONY: up down build setup restart logs

# Docker/Infrastructure commands
up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

restart: down up

logs:
	docker compose logs -f

setup: build up
	@echo "Waiting for containers to be ready..."
	@sleep 5
	./vendor/bin/sail artisan migrate --force
	./vendor/bin/sail artisan db:seed --force
	@echo "Setup complete! Access API at http://localhost"
	@echo "Access Swagger UI at http://localhost/api/documentation"

