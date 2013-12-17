#!/bin/bash

# Exit on first error
set -e

# Kill background processes on exit
trap 'kill $(jobs -p)' SIGINT SIGTERM EXIT

# Start docker daemon
docker -d -H 0.0.0.0:4243 -H unix:///var/run/docker.sock &
sleep 2

cd /home/travis/build/joelwurtz/docker-php && /home/travis/.phpenv/shims/php bin/phpunit -c phpunit.xml.dist