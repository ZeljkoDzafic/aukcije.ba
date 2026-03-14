# Hetzner Minimal Architecture Plan

Date: 2026-03-13

## Goal

Deploy a low-cost demo/funding-stage infrastructure on Hetzner using:

- `app-01`: public application server
- `db-01`: database server
- `ops-01`: operations/helper server

Initial sizing:

- `3 x CAX11`
- backups enabled on all 3 servers
- private network enabled

This setup is intentionally tight and should be treated as a starting point that can be scaled later.

## Estimated Cost

Based on Hetzner pricing effective April 1, 2026:

- `CAX11`: `$5.49/month` each
- `3 x CAX11`: `$16.47/month`
- backups: `20%` of server price = `$3.29/month`
- total: `~$19.76/month`

Optional later additions:

- Volume for database data
- Object Storage for offsite backups

## Server Layout

### `db-01`

Purpose:

- central relational databases
- lightweight database monitoring
- backup jobs

Services:

- `postgres`
- `mysql`
- `pgbouncer`
- `postgres_exporter`
- `mysqld_exporter`
- backup job via `restic` or dump-based cron
- `node-exporter`

Notes:

- expose `5432` and `3306` only on private network
- use separate Docker volumes for Postgres and MySQL
- keep this server stable and low-change

Suggested folders:

```text
/srv/db
/srv/db/postgres
/srv/db/mysql
/srv/db/backups
```

### `app-01`

Purpose:

- host public applications
- reverse proxy
- shared Redis

Services:

- `caddy`
- `redis`
- `aukcije.ba`
- `pedigre`
- `sarenasfera_platforma`
- `knjigovodstvo`
- app workers only where needed
- `node-exporter`
- `cadvisor`

Deployment rules:

- do not build images on server
- build in GitHub Actions
- push to `GHCR`
- deploy with `docker compose pull && docker compose up -d`

Suggested folders:

```text
/srv/proxy
/srv/apps/aukcije
/srv/apps/pedigre
/srv/apps/sarenasfera
/srv/apps/knjigovodstvo
```

### `ops-01`

Purpose:

- lightweight ops tooling
- monitoring/status dashboards
- automation
- GitHub runner
- trimmed self-hosted Supabase

Services:

- `supabase` trimmed
- `n8n`
- `uptime-kuma`
- `beszel`
- `github-actions runner`
- `node-exporter`
- `cadvisor`

What is intentionally excluded in phase 1:

- `grafana`
- `prometheus`
- `loki`
- `coroot`
- `livekit`
- full Supabase stack

Suggested folders:

```text
/srv/ops/uptime-kuma
/srv/ops/beszel
/srv/ops/n8n
/srv/ops/supabase
/srv/ops/github-runner
```

## Supabase Plan

Use a trimmed self-hosted Supabase footprint on `ops-01`.

Keep if needed:

- `studio`
- `kong`
- `auth`
- `rest`
- `meta`
- `postgres`
- `pooler`

Disable initially:

- `analytics`
- `logflare`
- `functions`
- `imgproxy`
- `vector`
- `realtime` unless required
- `storage` unless required

Important:

- Prefer keeping application databases on `db-01`
- Use Supabase primarily for Supabase-specific workloads instead of mixing all main app data into it

## Monitoring Choice

Use lightweight monitoring instead of Grafana-based stack in phase 1.

Recommended:

- `Uptime Kuma` for uptime, health checks, alerts
- `Beszel` for server/container CPU, RAM, disk and basic container visibility

Reason:

- significantly lower overhead than `Grafana + Prometheus + Loki`
- better fit for `CAX11`

## Networking

Create one private Hetzner network, for example:

- CIDR: `10.10.0.0/16`

Suggested private IPs:

- `app-01`: `10.10.0.10`
- `db-01`: `10.10.0.20`
- `ops-01`: `10.10.0.30`

Firewall rules:

### `app-01`

- allow `22` only from admin IP
- allow `80` public
- allow `443` public

### `db-01`

- allow `22` only from admin IP
- allow `5432` only from private network
- allow `3306` only from private network

### `ops-01`

- allow `22` only from admin IP
- keep admin/service ports private where possible
- prefer access through reverse proxy auth or Tailscale

## Base OS Setup

Use:

- Ubuntu 24.04
- SSH key only
- backups enabled
- Docker Engine
- Docker Compose plugin
- `ufw`
- `fail2ban`

Base install commands:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y ca-certificates curl gnupg ufw fail2ban git
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
sudo apt install -y docker-compose-plugin
```

## Compose Skeletons

### `db-01`

```yaml
services:
  postgres:
    image: postgres:16
    restart: unless-stopped
    environment:
      POSTGRES_DB: app
      POSTGRES_USER: app
      POSTGRES_PASSWORD: change-me
    volumes:
      - /srv/db/postgres:/var/lib/postgresql/data
    ports:
      - "10.10.0.20:5432:5432"

  mysql:
    image: mysql:8.4
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: change-me
    command: --default-authentication-plugin=mysql_native_password
    volumes:
      - /srv/db/mysql:/var/lib/mysql
    ports:
      - "10.10.0.20:3306:3306"

  pgbouncer:
    image: edoburu/pgbouncer
    restart: unless-stopped
    depends_on:
      - postgres
```

### `app-01`

```yaml
services:
  caddy:
    image: caddy:2
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - /srv/proxy/Caddyfile:/etc/caddy/Caddyfile
      - /srv/proxy/data:/data
      - /srv/proxy/config:/config

  redis:
    image: redis:7-alpine
    restart: unless-stopped

  aukcije:
    image: ghcr.io/zeljkodzafic/aukcije.ba-php:main
    restart: unless-stopped
    env_file:
      - /srv/apps/aukcije/.env
    depends_on:
      - redis

  aukcije-worker:
    image: ghcr.io/zeljkodzafic/aukcije.ba-php:main
    restart: unless-stopped
    command: php artisan queue:work --sleep=3 --tries=3
    env_file:
      - /srv/apps/aukcije/.env
    depends_on:
      - redis
```

### `ops-01`

```yaml
services:
  uptime-kuma:
    image: louislam/uptime-kuma:1
    restart: unless-stopped
    volumes:
      - /srv/ops/uptime-kuma:/app/data

  n8n:
    image: n8nio/n8n:latest
    restart: unless-stopped
    environment:
      N8N_HOST: n8n.example.com
      N8N_PROTOCOL: https
    volumes:
      - /srv/ops/n8n:/home/node/.n8n

  beszel:
    image: henrygd/beszel:latest
    restart: unless-stopped
    volumes:
      - /srv/ops/beszel:/beszel_data
      - /var/run/docker.sock:/var/run/docker.sock:ro
```

## CI/CD Plan

Use GitHub Actions per repository:

1. run tests
2. build Docker image
3. push image to `GHCR`
4. deploy over SSH to target server

Deploy pattern:

```bash
cd /srv/apps/aukcije
docker compose pull
docker compose up -d
docker image prune -af --filter "until=72h"
```

Suggested GitHub secrets:

- `SSH_HOST`
- `SSH_USER`
- `SSH_KEY`
- `SSH_PORT`
- app-specific environment secrets

Runner notes:

- keep only one GitHub self-hosted runner initially
- set low concurrency
- avoid local Docker builds on `ops-01`
- use it mainly for deploy/maintenance/internal access tasks

## Implementation Order

1. Buy `app-01`, `db-01`, `ops-01` as `CAX11`
2. Enable backups and private network
3. Install Docker and base packages on all servers
4. Deploy `db-01`
5. Deploy `app-01` with Caddy and one application first
6. Deploy `ops-01` with `Uptime Kuma`, `Beszel`, and GitHub runner
7. Add trimmed Supabase
8. Add `n8n`
9. Add remaining applications

## Scaling Plan

Start with `3 x CAX11`, then scale based on pressure:

- first upgrade target: `ops-01`
- second upgrade target: `app-01`
- keep `db-01` simple until workload justifies stronger separation

Likely scale path:

- `CAX11` -> `CAX21` for `ops-01`
- `CAX11` -> `CAX21` for `app-01`

## Next Required Inputs

After servers are created, collect:

- public IPs
- private IPs
- domain to app mapping
- which apps need PostgreSQL vs MySQL
- which apps require queues/workers
- whether Supabase needs `storage` and `realtime` immediately

Once that is available, the next step is to create:

- exact `docker-compose` files
- reverse proxy config
- SSH deploy workflows
- backup scripts
