#!/usr/bin/env bash
# EC2 user-data (cloud-init) — auto-provisions a web server on launch.
# Paste into the Launch Template "User data" field. Runs as root on first boot.
#
# Per-instance label comes from an EC2 tag named "ServerLabel" (e.g. "SERVER 1").
#   Launch Template -> Resource tags -> add Key=ServerLabel Value="SERVER 1"
#   AND enable: Advanced details -> Metadata -> "Instance tags in metadata: Enabled"
# Falls back to the instance-id if the tag is missing.
#
# Ubuntu 24.04 / 22.04 AMI. Fill REPO_URL + the RDS_* values before use.

set -euxo pipefail
exec > /var/log/cloud-init-app.log 2>&1

APP_DIR=/var/www/cloud-app
REPO_URL="https://github.com/your-user/your-repo.git"   # <-- set this

# --- RDS connection (shared) ---
RDS_HOST="database-tubes-cc.cngyusqaygyp.ap-southeast-1.rds.amazonaws.com"
RDS_DB="laravel"
RDS_USER="Andika"
RDS_PASS='!MyPassword2026'
ALB_DNS="http://your-alb-dns-name"                      # <-- set to your ALB DNS

export DEBIAN_FRONTEND=noninteractive

# ---- Per-instance label from IMDSv2 tag "ServerLabel" ----
TOKEN=$(curl -sS -X PUT "http://169.254.169.254/latest/api/token" \
  -H "X-aws-ec2-metadata-token-ttl-seconds: 300" || true)
SERVER_LABEL=$(curl -sS -H "X-aws-ec2-metadata-token: $TOKEN" \
  "http://169.254.169.254/latest/meta-data/tags/instance/ServerLabel" || true)
if [ -z "${SERVER_LABEL:-}" ]; then
  IID=$(curl -sS -H "X-aws-ec2-metadata-token: $TOKEN" \
    "http://169.254.169.254/latest/meta-data/instance-id" || echo unknown)
  SERVER_LABEL="WEB-$IID"
fi

# ---- Packages ----
apt-get update -y
apt-get install -y software-properties-common curl git unzip
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y nginx \
  php8.3-fpm php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-mysql php8.3-bcmath php8.3-intl php8.3-zip

# ---- Composer ----
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# ---- Code ----
mkdir -p "$APP_DIR"
git clone "$REPO_URL" "$APP_DIR"
cd "$APP_DIR"
composer install --no-dev --optimize-autoloader

# ---- .env ----
cp .env.example .env
php artisan key:generate --force
cat >> .env <<ENV

APP_ENV=production
APP_DEBUG=false
APP_URL=$ALB_DNS
APP_SERVER_NAME="$SERVER_LABEL"
DB_CONNECTION=mysql
DB_HOST=$RDS_HOST
DB_PORT=3306
DB_DATABASE=$RDS_DB
DB_USERNAME=$RDS_USER
DB_PASSWORD="$RDS_PASS"
ENV
# Strip the original sqlite/template DB_* lines so the appended block wins.
sed -i '/^DB_CONNECTION=sqlite/d' .env

php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ---- Nginx ----
cp deploy/nginx-cloud-app.conf /etc/nginx/sites-available/cloud-app
ln -sf /etc/nginx/sites-available/cloud-app /etc/nginx/sites-enabled/cloud-app
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl enable --now php8.3-fpm nginx
systemctl reload nginx

# NOTE: do NOT run migrations here — many instances launch at once and would race.
# Run `php artisan migrate --force` once from a single node (or a bastion) after the
# first launch. Health endpoint for the ALB: GET /up
echo "Provisioned $SERVER_LABEL"
