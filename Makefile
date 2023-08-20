IMAGE_NAME=opencodeco/rinha-de-backend:2023-q3

default: up

.PHONY: setup
setup:
	docker compose run --rm setup

.PHONY: migrate
migrate:
	docker compose exec db bash -c "mysql twilight < /var/www/twilight.sql"

.PHONY: up
up:
	docker compose up -d

.PHONY: stress
stress:
	sh ./gatling/run.sh
