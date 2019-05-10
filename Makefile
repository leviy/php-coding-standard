REPORT = full

vendor: composer.lock
	composer install

composer.lock: composer.json
	composer update

.PHONY: check
check: vendor
	vendor/bin/phpmd src/ text phpmd.xml
	vendor/bin/phpcs tests/correct/
	vendor/bin/phpcs $(shell find tests/incorrect/* | sort) --report=$(REPORT) --report-file=phpcs.log --report-width=120; diff tests/expected.log phpcs.log

tests/expected.log: tests/incorrect/*
	vendor/bin/phpcs $(shell find tests/incorrect/* | sort) --report=$(REPORT) --report-file=$@ --report-width=120
