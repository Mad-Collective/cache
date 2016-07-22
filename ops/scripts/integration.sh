#!/bin/bash

function test {
  PHP="php-$1"
  $PHP bin/behat
}

for version in 5.5 5.6 7.0; do
  printf "\nTesting PHP $version\n"
  test $version
done