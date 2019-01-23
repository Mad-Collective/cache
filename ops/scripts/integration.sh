#!/bin/bash

function test {
  PHP="php-$1"
  $PHP bin/behat
}

for version in 7.1 7.2; do
  printf "\nTesting PHP $version\n"
  test $version
done