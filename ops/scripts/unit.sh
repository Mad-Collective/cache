#!/bin/bash

function test {
  PHP="php-$1"
  $PHP bin/phpspec run
}

for version in 7.1 7.2; do
  echo "Testing PHP $version"
  test $version
done