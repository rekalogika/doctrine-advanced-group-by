PHP=php
COMPOSER=composer

-include local.mk

.PHONY: test 
test: composer-dump phpstan psalm phpunit

.PHONY: composer-dump
composer-dump:
	$(COMPOSER) dump-autoload

.PHONY: phpstan
phpstan:
	$(PHP) vendor/bin/phpstan analyse

.PHONY: psalm
psalm:
	$(PHP) vendor/bin/psalm

.PHONY: phpunit
phpunit:
	$(eval c ?=)
	$(PHP) vendor/bin/phpunit $(c)

.PHONY: rector
rector:
	$(PHP) vendor/bin/rector process > rector.log
	make php-cs-fixer

.PHONY: php-cs-fixer
php-cs-fixer: tools/php-cs-fixer
	$(PHP) $< fix --config=.php-cs-fixer.dist.php --verbose --allow-risky=yes

.PHONY: tools/php-cs-fixer
tools/php-cs-fixer:
	phive install php-cs-fixer
