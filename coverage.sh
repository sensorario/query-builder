#!/bin/bash
php ./bin/phpunit --coverage-html /tmp/report
open /tmp/report/index.html
