#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
IMAGE="${DASHGOALS_PHP_IMAGE:-php:5.6-cli}"
cd "$ROOT"

if ! command -v docker >/dev/null 2>&1; then
  echo "docker is required to run PHPUnit on PHP 5.6" >&2
  exit 1
fi

run_docker() {
  if docker info >/dev/null 2>&1; then
    docker "$@"
  else
    sudo docker "$@"
  fi
}

run_docker run --rm \
  -v "$ROOT:/app" \
  -w /app \
  -e TZ=UTC \
  "$IMAGE" \
  bash -lc '
    set -euo pipefail
    PHAR="tests/tools/phpunit-4.8.36.phar"
    if [ ! -f "$PHAR" ]; then
      curl -sSL -o "$PHAR" https://phar.phpunit.de/phpunit-4.8.36.phar
    fi
    if [ -f tests/tools/phpunit-4.8.36.phar.sha256 ]; then
      (cd tests/tools && sha256sum -c phpunit-4.8.36.phar.sha256)
    fi
    if ! php -m | grep -q tokenizer; then
      echo "PHP tokenizer extension is required for PHPUnit" >&2
      exit 1
    fi
    php "$PHAR" --configuration phpunit.xml.dist "$@"
  ' -- "$@"
