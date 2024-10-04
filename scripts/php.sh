#!/bin/sh

set -ex

PHP_VERSION="${PHP_VERSION:-"8.3"}"
PHP_IMAGE="${COMPOSER_IMAGE}"

docker run --rm -it \
  -v "${PWD}:${PWD}" \
  -w "${PWD}" \
  -e "XDG_CACHE_HOME=/tmp" \
  --tmpfs "/tmp" \
  -u "$(id -u):$(id -g)" \
  ${PHP_IMAGE} "$@"
