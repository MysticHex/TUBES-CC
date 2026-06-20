# EC2 Deployment — Cloud Computing Laravel App

No Docker. Stack per web server: **Nginx → PHP-FPM 8.3 → Laravel**. Shared **RDS MySQL**. Traffic via **Application Load Balancer**.

```
                 ┌──────────────────────────┐
   Internet ───► │  Application Load Balancer │  (HTTP :80, health /up)
                 └─────────┬─────────┬───────┘
                           ▼         ▼
                   ┌────────────┐ ┌────────────┐
                   │ EC2 Web 1  │ │ EC2 Web 2  │   Nginx + PHP-FPM
                   │ SERVER 1   │ │ SERVER 2   │
                   └─────┬──────┘ └─────┬──────┘
                         └──────┬───────┘
                                ▼
                        ┌───────────────┐
                        │  RDS MySQL     │  (private, SG allows web SG)
                        └───────────────┘
```

## Files
| File | Purpose |
|------|---------|
| `setup-ec2.sh` | One-shot provisioning per web server |
| `nginx-cloud-app.conf` | Nginx vhost (docroot `public/`, PHP-FPM, `/up` health) |
| `laravel-worker.service` | Optional systemd queue worker |

## Per web server
```bash
# On EC2 Web 1
sudo bash deploy/setup-ec2.sh "SERVER 1"
# On EC2 Web 2
sudo bash deploy/setup-ec2.sh "SERVER 2"
```
Then edit `/var/www/cloud-app/.env` on each node:
```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://<your-alb-dns-name>
APP_SERVER_NAME="SERVER 1"          # "SERVER 2" on the other node

DB_CONNECTION=mysql
DB_HOST=database-tubes-cc.cngyusqaygyp.ap-southeast-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=Andika
DB_PASSWORD="!MyPassword2026"
```
Re-cache after editing `.env`:
```bash
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

> `APP_SERVER_NAME` (not `SERVER_NAME`) — the bare name collides with `$_SERVER['SERVER_NAME']` (the Nginx host) and gets shadowed.

## Migrate once
Run from **one** node only (or from your machine while RDS is reachable):
```bash
php artisan migrate --force
# first-time data:
php artisan db:seed --force
```
The other node shares the same RDS — do **not** migrate again.

## ALB setup
- **Target group:** protocol HTTP, port **80**, targets = both web EC2 instances.
- **Health check:** path **`/up`** (Laravel's built-in health route), success code **200**.
- **Listener:** HTTP :80 → forward to the target group (add HTTPS :443 + ACM cert for production).
- **Stickiness:** leave **off** to see the dashboard's server label alternate between SERVER 1 / SERVER 2 on refresh (the load-balancing demo). Turn on only if you want sticky sessions.

## Security groups
- **Web EC2 SG:** inbound 80 from the **ALB SG**; inbound 22 from your IP (SSH).
- **RDS SG:** inbound 3306 from the **web EC2 SG** (already configured as `rds-ec2-2`). No public access needed in production.
- Sessions are stored in MySQL (`SESSION_DRIVER=database`), so they work across both web servers behind the ALB.

## Revert the temporary local-access rule
The laptop test opened RDS publicly. For production hygiene:
1. RDS → Modify → **Publicly accessible = No**.
2. Remove the `182.10.98.172/32` inbound rule from `sg-07d422b54515309ba`.

## Auto-provision on launch (Launch Template user-data)
Use `deploy/user-data.sh` instead of running `setup-ec2.sh` by hand:
1. Edit `REPO_URL`, `ALB_DNS`, and the `RDS_*` values at the top.
2. Launch Template → **Advanced details → User data** → paste the script.
3. Launch Template → **Resource tags** → add `ServerLabel = "SERVER 1"` (and a second template/tag `"SERVER 2"`), and enable **Metadata → Instance tags in metadata: Enabled** so the script can read its own label via IMDSv2.
4. New instances self-configure on first boot. Logs: `/var/log/cloud-init-app.log`.

The script intentionally **does not migrate** (instances launch in parallel and would race). Run `php artisan migrate --force` once from a single node after the first launch.

## HTTPS (ACM + ALB)
TLS terminates at the ALB — Nginx stays plain HTTP:80, no cert on the boxes.

1. **Request a cert** in ACM (same region as the ALB) for your domain; validate via DNS.
2. **Add a 443 listener** forwarding to the same target group:
```bash
aws elbv2 create-listener \
  --load-balancer-arn <alb-arn> \
  --protocol HTTPS --port 443 \
  --ssl-policy ELBSecurityPolicy-TLS13-1-2-2021-06 \
  --certificates CertificateArn=<acm-cert-arn> \
  --default-actions Type=forward,TargetGroupArn=<target-group-arn>
```
3. **Redirect HTTP→HTTPS** — modify the existing :80 listener to redirect:
```bash
aws elbv2 modify-listener --listener-arn <http-listener-arn> \
  --default-actions '[{"Type":"redirect","RedirectConfig":{"Protocol":"HTTPS","Port":"443","StatusCode":"HTTP_301"}}]'
```
4. **ALB security group:** allow inbound 443 (and 80 for the redirect) from `0.0.0.0/0`.
5. In each `.env`, set `APP_URL=https://<your-domain>`. Behind a TLS-terminating ALB, also trust the proxy so Laravel generates `https://` URLs — add to `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```
(`'*'` trusts the VPC-internal ALB; scope to the ALB subnet CIDRs if you prefer.)

## Smoke test
```bash
curl -i http://localhost/up                 # 200 from each node
curl -i http://<alb-dns>/login              # 200 via the load balancer
curl -i https://<your-domain>/up            # 200 once HTTPS listener is live
```
Default login: `admin@example.com` / `password`.
