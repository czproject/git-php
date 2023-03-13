src_dir = src/
tester_bin = vendor/bin/tester
tests_dir = tests/
coverage_name = $(tests_dir)coverage.html
php_bin = php
phpstan_bin = phpstan

.PHONY: test coverage clean phpstan
test:
		@$(tester_bin) -p $(php_bin) -C $(tests_dir)

coverage:
		@$(tester_bin) -p $(php_bin) -C -d zend_extension=xdebug --coverage $(coverage_name) --coverage-src $(src_dir) $(tests_dir)

clean:
		@rm -f $(coverage_name)

phpstan:
		@$(phpstan_bin)
