IMAGE_NAME=opencodeco/rinha-de-backend:2023-q3

default: down setup up

.PHONY: setup
setup:
	docker compose run --rm setup

.PHONY: up
up:
	docker compose up

.PHONY: down
down:
	docker compose down --remove-orphans --volumes

.PHONY: bash
bash:
	docker compose run --rm setup bash

.PHONY: migrate
migrate:
	docker compose exec db bash -c "mysql twilight < /var/www/twilight.sql"

.PHONY: stress
stress:
	sh ./gatling/run.sh
