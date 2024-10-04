#!/bin/sh

set -e

BASEDIR="$(dirname "$0")"
"${BASEDIR}/php.sh" ./vendor/bin/psalm "$@"

