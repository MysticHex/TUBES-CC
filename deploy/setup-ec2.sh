#!/usr/bin/env bash
# Provision one EC2 web server for the Cloud Computing Laravel app.
# No Docker. Nginx + PHP-FPM 8.3 + Composer. Ubuntu 24.04 / 22.04.
#
# Usage:
#   sudo bash setup-ec2.sh "SERVER 1"     # run "SERVER 2" on the other node
#
# Amazon Linux 2023 differences are noted inline.

set -euo pipefail

APP_DIR=/var/www/cloud-app
SERVER_LABEL="${1:?Pass the server label, e.g. \"SERVER 1\"}"
REPO_URL="https://github.com/your-user/your-repo.git"   # <-- set this (or rsync code to $APP_DIR)

echo ">> Installing packages..."
# --- Ubuntu ---
export DEBIAN_FRONTEND=noninteractive
sudo apt-get update -y
sudo apt-get install -y software-properties-common curl git unzip
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update -y
sudo apt-get install -y nginx \
  php8.3-fpm php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-mysql php8.3-bcmath php8.3-intl php8.3-zip
# --- Amazon Linux 2023 (alternative) ---
# sudo dnf install -y nginx git unzip \
#   php php-fpm php-mbstring php-xml php-pdo php-mysqlnd php-bcmath php-intl php-zip

echo ">> Installing Composer..."
if ! command -v composer >/dev/null 2>&1; then
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
fi

echo ">> Fetching application code..."
sudo mkdir -p "$APP_DIR"
sudo chown -R "$USER":www-data "$APP_DIR"
if [ ! -d "$APP_DIR/.git" ]; then
  git clone "$REPO_URL" "$APP_DIR"
else
  git -C "$APP_DIR" pull --ff-only
fi
cd "$APP_DIR"

echo ">> Installing PHP dependencies (no dev, optimized)..."
composer install --no-dev --optimize-autoloader

echo ">> Configuring environment..."
if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate --force
fi
# Set this node's identity. Edit .env afterwards to fill the RDS DB creds.
if grep -q '^APP_SERVER_NAME=' .env; then
  sudo sed -i "s|^APP_SERVER_NAME=.*|APP_SERVER_NAME=\"$SERVER_LABEL\"|" .env
else
  echo "APP_SERVER_NAME=\"$SERVER_LABEL\"" >> .env
fi

echo ">> !! Edit $APP_DIR/.env now: set DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD to the RDS instance,"
echo ">>    APP_ENV=production, APP_DEBUG=false, APP_URL=http://<your-alb-dns>"

echo ">> Caching config/routes/views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">> Permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo ">> Nginx vhost..."
sudo cp deploy/nginx-cloud-app.conf /etc/nginx/sites-available/cloud-app
sudo ln -sf /etc/nginx/sites-available/cloud-app /etc/nginx/sites-enabled/cloud-app
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl enable --now php8.3-fpm nginx
sudo systemctl reload nginx

echo ">> DONE on $SERVER_LABEL."
echo ">> Run database migrations from ONE node only:  php artisan migrate --force"
echo ">> (Schema already seeded? skip migrate.)  Health check: curl -i http://localhost/up"
