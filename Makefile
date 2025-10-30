.PHONY: help build up down restart logs shell artisan composer migrate seed fresh install

# Colors
BLUE := \033[0;34m
GREEN := \033[0;32m
NC := \033[0m # No Color

help: ## Show this help message
	@echo '${BLUE}Usage:${NC}'
	@echo '  make ${GREEN}<target>${NC}'
	@echo ''
	@echo '${BLUE}Available targets:${NC}'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  ${GREEN}%-15s${NC} %s\n", $$1, $$2}'

install: ## Initial setup - build and initialize application
	@echo "${BLUE}Running initial setup...${NC}"
	@chmod +x docker-setup.sh
	@./docker-setup.sh

build: ## Build Docker containers
	@echo "${BLUE}Building Docker containers...${NC}"
	docker-compose build

up: ## Start Docker containers
	@echo "${BLUE}Starting Docker containers...${NC}"
	docker-compose up -d

down: ## Stop Docker containers
	@echo "${BLUE}Stopping Docker containers...${NC}"
	docker-compose down

restart: down up ## Restart Docker containers

logs: ## View logs from all containers
	docker-compose logs -f

logs-app: ## View application logs
	docker-compose logs -f app

logs-nginx: ## View nginx logs
	docker-compose logs -f nginx

logs-mysql: ## View MySQL logs
	docker-compose logs -f mysql

shell: ## Access application container shell
	docker-compose exec app bash

shell-mysql: ## Access MySQL CLI
	docker-compose exec mysql mysql -u laravel -p

phpmyadmin: ## Open phpMyAdmin in browser
	@echo "${BLUE}Opening phpMyAdmin at http://localhost:8080${NC}"
	@open http://localhost:8080 || xdg-open http://localhost:8080 || echo "Please visit http://localhost:8080"

open: ## Open application in browser
	@echo "${BLUE}Opening application at http://localhost:8000${NC}"
	@open http://localhost:8000 || xdg-open http://localhost:8000 || echo "Please visit http://localhost:8000"

artisan: ## Run artisan command (use: make artisan cmd="migrate")
	docker-compose exec app php artisan $(cmd)

composer: ## Run composer command (use: make composer cmd="install")
	docker-compose exec app composer $(cmd)

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Drop all tables and re-run migrations
	docker-compose exec app php artisan migrate:fresh

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

fresh: migrate-fresh seed ## Fresh migration with seeding

cache-clear: ## Clear all caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

permissions: ## Fix storage and cache permissions
	docker-compose exec app chmod -R 775 storage bootstrap/cache

ps: ## Show container status
	docker-compose ps

stats: ## Show container resource usage
	docker stats

clean: ## Remove all containers, volumes, and images
	@echo "${BLUE}Cleaning up Docker resources...${NC}"
	docker-compose down -v
	docker-compose rm -f
	@echo "${GREEN}Cleanup completed${NC}"

rebuild: clean build up ## Clean rebuild of all containers
	@echo "${GREEN}Rebuild completed${NC}"
