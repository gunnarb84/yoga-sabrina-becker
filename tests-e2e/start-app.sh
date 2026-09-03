#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

# Load the end-to-end environment.
set -a
source tests-e2e/.env.e2e
set +a

export APP_BASE_PATH=src

php src/artisan serve --host=127.0.0.1 --port=8001 --no-interaction
