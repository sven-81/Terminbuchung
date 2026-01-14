.PHONY: test analyse cs-fix swagger migrate seed fresh all

# Development commands
test:
	./vendor/bin/sail artisan test

test-unit:
	./vendor/bin/sail artisan test --testsuite=Unit

test-system:
	./vendor/bin/sail artisan test --testsuite=Feature

analyse:
	./vendor/bin/sail composer exec phpstan analyse

cs-fix:
	./vendor/bin/sail composer exec pint

swagger:
	./vendor/bin/sail artisan l5-swagger:generate

# Database commands
migrate:
	./vendor/bin/sail artisan migrate

seed:
	./vendor/bin/sail artisan db:seed

fresh:
	./vendor/bin/sail artisan migrate:fresh --seed

# Combined commands
all: cs-fix analyse test swagger

ci: analyse test

