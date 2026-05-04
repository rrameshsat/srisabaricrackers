#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
ROOT_DIR=$(cd "$SCRIPT_DIR/.." && pwd)
CORE_DIR="$ROOT_DIR/core"

echo "[setup_core.sh] Setting up core at $CORE_DIR"

if [[ ! -d "$CORE_DIR" ]]; then
  echo "Core directory not found: $CORE_DIR"; exit 1
fi

function run_composer_install() {
  if [[ -f "$CORE_DIR/composer.phar" ]]; then
    php "$CORE_DIR/composer.phar" install --no-interaction
  elif command -v composer >/dev/null 2>&1; then
    composer install --no-interaction
  else
    echo "Composer not found. Attempting to fetch composer.phar into core..."
    if command -v curl >/dev/null 2>&1; then
      curl -sS https://getcomposer.org/composer.phar -o "$CORE_DIR/composer.phar"
      php "$CORE_DIR/composer.phar" install --no-interaction
    elif command -v wget >/dev/null 2>&1; then
      wget -q -O "$CORE_DIR/composer.phar" https://getcomposer.org/composer.phar
      php "$CORE_DIR/composer.phar" install --no-interaction
    else
      echo "Cannot fetch composer.phar. Please ensure Composer is available in PATH or in core/Composer.phar"; exit 1
    fi
  fi
}

cd "$CORE_DIR"

echo "[setup_core.sh] Verifying PHP availability..."
if ! command -v php >/dev/null 2>&1; then
  echo "PHP CLI not found in PATH. Please install PHP and ensure it is available."; exit 1
fi

echo "[setup_core.sh] Running composer install..."
run_composer_install

autoloadPath="$CORE_DIR/vendor/autoload.php"
if [[ ! -f "$autoloadPath" ]]; then
  echo "Autoload not found at $autoloadPath. Composer install may have failed or vendor directory is missing."; exit 1
fi
echo "[setup_core.sh] Autoload found: $autoloadPath"

if [[ -f "$CORE_DIR/artisan" ]]; then
  echo "[setup_core.sh] Running migrations..."
  php "$CORE_DIR/artisan" migrate --force || true
  echo "[setup_core.sh] Running seeds..."
  php "$CORE_DIR/artisan" db:seed --class=UpiConfigSeeder || true
else
  echo "[setup_core.sh] Artisan not found; skipping migrations/seed."
fi

echo "[setup_core.sh] Core setup complete."
