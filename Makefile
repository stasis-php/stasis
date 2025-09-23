.SILENT:
.PHONY: $(MAKECMDGOALS)

test:
	$(MAKE) phpcs
	echo "\n"
	$(MAKE) phpstan

phpcs:
	echo "\033[7m # \033[0m \033[1mPHP CS Fixer\033[0m"
	vendor/bin/php-cs-fixer --config=phpcs.php --ansi --show-progress=dots --diff check

phpcs-fix:
	echo "\033[7m # \033[0m \033[1mPHP CS Fixer (fix)\033[0m"
	vendor/bin/php-cs-fixer --config=phpcs.php --ansi --show-progress=dots --diff fix

phpstan:
	echo "\033[7m # \033[0m \033[1mPHPStan\033[0m"
	vendor/bin/phpstan --ansi --memory-limit=1G --no-progress
