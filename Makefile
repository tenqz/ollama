SHELL := /bin/bash

.DEFAULT_GOAL := help
COMPOSER ?= composer

.PHONY: help check lint test stan

help:
	@echo "Available targets:"
	@echo "  make check  - run lint, stan, test (via composer check)"
	@echo "  make lint   - run PHPCS and PHP-CS-Fixer (dry run)"
	@echo "  make stan   - run PHPStan analysis"
	@echo "  make test   - run PHPUnit"

check:
	$(COMPOSER) run --no-interaction --ansi check

lint:
	$(COMPOSER) run --no-interaction --ansi check-style

stan:
	$(COMPOSER) run --no-interaction --ansi analyze

test:
	$(COMPOSER) run --no-interaction --ansi test


