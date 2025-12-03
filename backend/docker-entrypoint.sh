#!/bin/bash
set -e

wait_for_db() {
  local cmd="$@"
  local retries=10
  local delay=5
  echo "Waiting for database to be ready to execute: $cmd"
  for i in $(seq 1 $retries); do
    if $cmd; then
      echo "Command executed successfully: $cmd"
      return 0
    else
      echo "Attempt $i/$retries failed. Retrying in $delay seconds..."
      sleep $delay
    fi
  done
  echo "Failed to execute command after $retries attempts: $cmd"
  return 1
}

# Generate dev JWT keypair if missing (do not commit these keys)
if [ ! -f config/jwt/private.pem ] || [ ! -f config/jwt/public.pem ]; then
	echo "JWT keys not found — creating development keypair in config/jwt/"
	mkdir -p config/jwt
	# Generate a 4096-bit RSA private key and a public key
	openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096
	openssl rsa -in config/jwt/private.pem -pubout -out config/jwt/public.pem
	chmod 600 config/jwt/private.pem || true
fi

echo "Running migrations..."
wait_for_db php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:update --force || true

# Check if stations table is empty (always load data on first run)
STATION_COUNT=$(php bin/console dbal:run-sql "SELECT COUNT(*) FROM station" 2>/dev/null | tail -n 1 || echo "0")

if [ "$STATION_COUNT" = "0" ] || [ -z "$STATION_COUNT" ]; then
  echo "Loading data..."
  wait_for_db php bin/console app:load-data

  # Remove data files after successful load to keep container clean
  echo "Cleaning up data files..."
  rm -f /app/stations.json /app/distances.json || true
  echo "Data files removed"
else
  echo "Data already loaded ($STATION_COUNT stations found) - skipping data load"
fi

echo "Starting PHP-FPM..."
# By default use the PHP built-in webserver on port 8000 so the
# frontend dev server and nginx can reach an HTTP API at backend:8000.
# To use php-fpm instead set environment variable USE_PHP_FPM=1
if [ "${USE_PHP_FPM:-0}" = "1" ]; then
	echo "Starting PHP-FPM..."
	exec php-fpm
else
	echo "Starting PHP built-in server on :8000 (document root: public)"
	exec php -S 0.0.0.0:8000 -t public
fi
