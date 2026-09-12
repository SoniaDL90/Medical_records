#!/bin/sh
set -e

mkdir -p config/jwt

if [ ! -f config/jwt/private.pem ]; then
  php bin/console lexik:jwt:generate-keypair --skip-if-exists --no-interaction
fi

php bin/console cache:clear --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

exec php -S 0.0.0.0:8000 -t public
