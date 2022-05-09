mkdir test || true
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html test
