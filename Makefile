.PHONY: help install start stop restart test format analyse logs clean build shell composer artisan

# Default target
.DEFAULT_GOAL := help

# Colors for output
BOLD := \033[1m
GREEN := \033[1;32m
CYAN := \033[1;36m
YELLOW := \033[1;33m
BLUE := \033[1;34m
MAGENTA := \033[1;35m
NC := \033[0m # No Color

help: ## Show this help message
	@echo ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)     Laravel Microservice Template - Make Commands             $(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo ""
	@echo -e "$(BOLD)$(YELLOW)[Setup & Installation]$(NC)"
	@grep -E '^(install|setup):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(BOLD)$(GREEN)%-18s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(BOLD)$(YELLOW)[Docker Commands]$(NC)"
	@grep -E '^(start|stop|restart|build|ps|down-volumes):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(BOLD)$(BLUE)%-18s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(BOLD)$(YELLOW)[Logs & Monitoring]$(NC)"
	@grep -E '^logs.*:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(BOLD)$(CYAN)%-18s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(BOLD)$(YELLOW)[Database]$(NC)"
	@grep -E '^(migrate|migrate-fresh):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(BOLD)$(MAGENTA)%-18s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(BOLD)$(YELLOW)[Development Tools]$(NC)"
	@grep -E '^(shell|shell-mysql|shell-redis|composer|artisan|pma):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(BOLD)$(CYAN)%-18s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(BOLD)$(YELLOW)[Maintenance]$(NC)"
	@grep -E '^(clean|test|format|analyse):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(BOLD)$(GREEN)%-18s$(NC) %s\n", $$1, $$2}'
	@echo ""

install: ## Install dependencies and setup environment
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Installing dependencies...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	composer install
	npm install
	cp -n .env.example .env || true
	php artisan key:generate
	@echo -e "$(BOLD)$(GREEN)[OK] Installation complete!$(NC)"

start: ## Start Docker containers
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Starting containers...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	docker compose up -d
	@echo -e "$(BOLD)$(GREEN)[OK] Containers started!$(NC)"

stop: ## Stop Docker containers
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Stopping containers...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	docker compose down
	@echo -e "$(BOLD)$(GREEN)[OK] Containers stopped!$(NC)"

restart: stop start ## Restart Docker containers

build: ## Build Docker images
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Building Docker images...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	docker compose build
	@echo -e "$(BOLD)$(GREEN)[OK] Build complete!$(NC)"

test: ## Run tests
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Running tests...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	php artisan test

format: ## Format code with Laravel Pint
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Formatting code...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	php vendor/bin/pint

analyse: ## Run PHPStan static analysis
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Running static analysis...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	php vendor/bin/phpstan analyse

logs: ## Show Docker container logs
	docker compose logs -f

logs-app: ## Show app container logs
	docker compose logs -f app

logs-nginx: ## Show nginx container logs
	docker compose logs -f nginx

logs-mysql: ## Show MySQL container logs
	docker compose logs -f mysql

logs-redis: ## Show Redis container logs
	docker compose logs -f redis

clean: ## Clean cache and temporary files
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)                     Cleaning cache...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	docker compose exec app php artisan optimize:clear
	@echo -e "$(BOLD)$(GREEN)[OK] Cache cleared!$(NC)"

shell: ## Access app container shell
	docker compose exec app bash

shell-mysql: ## Access MySQL container shell
	docker compose exec mysql mysql -u${DB_USERNAME:-microservice} -p${DB_PASSWORD:-secret}

shell-redis: ## Access Redis CLI
	docker compose exec redis redis-cli

composer: ## Run composer command (usage: make composer CMD="install")
	docker compose exec app composer $(CMD)

artisan: ## Run artisan command (usage: make artisan CMD="migrate")
	docker compose exec app php artisan $(CMD) --ansi

migrate: ## Run database migrations
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Running migrations...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	docker compose exec app php artisan migrate --ansi

migrate-fresh: ## Fresh migration with seed
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)               Fresh migration with seeding...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	docker compose exec app php artisan migrate:fresh --seed --ansi

pma: ## Open phpMyAdmin in browser
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(GREEN)phpMyAdmin Access:$(NC)"
	@echo -e "  $(BOLD)$(CYAN)URL:$(NC)      http://localhost:$${PHPMYADMIN_PORT:-8080}"
	@echo -e "  $(BOLD)$(YELLOW)Username:$(NC) $${DB_USERNAME:-microservice}"
	@echo -e "  $(BOLD)$(YELLOW)Password:$(NC) $${DB_PASSWORD:-secret}"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"

ps: ## Show running containers
	docker compose ps

down-volumes: ## Stop containers and remove volumes
	@echo -e ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)          Stopping containers and removing volumes...$(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(YELLOW)[WARNING] This will delete all data in volumes!$(NC)"
	docker compose down -v
	@echo -e "$(BOLD)$(GREEN)[OK] Containers stopped and volumes removed!$(NC)"

setup: install build start migrate ## Complete setup (install + build + start + migrate)
	@echo ""
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo -e "$(BOLD)$(CYAN)                    SETUP COMPLETE!                             $(NC)"
	@echo -e "$(BOLD)$(CYAN)================================================================$(NC)"
	@echo ""
	@echo -e "$(BOLD)$(GREEN)Application is ready!$(NC)"
	@echo -e "  $(BOLD)$(CYAN)App:$(NC)         http://localhost:$${APP_PORT:-8000}"
	@echo -e "  $(BOLD)$(CYAN)phpMyAdmin:$(NC)  http://localhost:$${PHPMYADMIN_PORT:-8080}"
	@echo ""
