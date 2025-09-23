.SILENT:
.PHONY: $(MAKECMDGOALS)

test:
	$(MAKE) phpcs

phpcs:
	echo "\033[7m # \033[0m \033[1mPHP CS Fixer\033[0m"
	vendor/bin/php-cs-fixer --config=phpcs.php --ansi --show-progress=dots --diff check

phpcs-fix:
	echo "\033[7m # \033[0m \033[1mPHP CS Fixer (fix)\033[0m"
	vendor/bin/php-cs-fixer --config=phpcs.php --ansi --show-progress=dots --diff fix
