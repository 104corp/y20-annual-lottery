type = "real"

.PHONY: key.genrate
key.generate:
	php artisan key:generate

.PHONY: key.genrate.test
key.generate.test:
	php artisan key:generate  --env=testing

.PHONY: program.start
program.start:
	touch database/database.sqlite && php artisan program:start --type=$(type)

.PHONY: program.print-result
program.print-result:
	php artisan program:print-result

.PHONY: test.start
test.start:
	php artisan key:generate  --env=testing
	touch database/database_test.sqlite && php artisan test
	sed -i.bak 's/base64.*//g' .env.testing
	rm .env.testing.bak

.PHONY: api-docs
api-docs:
	php artisan l5-swagger:generate
