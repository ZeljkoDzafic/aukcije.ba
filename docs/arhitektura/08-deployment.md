# 08 - Deployment & Infrastructure

## Environment Architecture

```
                    ┌──── Cloudflare CDN/WAF ────┐
                    │                             │
                    ▼                             ▼
            ┌──────────────┐             ┌──────────────┐
            │  Web Server  │             │  WebSocket   │
            │  (Nginx)     │             │  (Reverb)    │
            │  :443 HTTPS  │             │  :8080 WSS   │
            └──────┬───────┘             └──────┬───────┘
                   │                            │
                   ▼                            │
            ┌──────────────┐                    │
            │  PHP-FPM     │◄───────────────────┘
            │  (Laravel)   │
            └──┬────┬──┬───┘
               │    │  │
     ┌─────────┘    │  └─────────┐
     ▼              ▼            ▼
┌──────────┐ ┌──────────┐ ┌──────────┐
│PostgreSQL│ │  Redis   │ │Meilisearch│
│  :5432   │ │  :6379   │ │  :7700   │
└──────────┘ └──────────┘ └──────────┘

     ┌──────────────┐    ┌──────────────┐
     │   Horizon    │    │  Scheduler   │
     │ (Queue)      │    │  (Cron)      │
     └──────────────┘    └──────────────┘

     ┌──────────────┐
     │   AWS S3     │
     │ (Images)     │
     └──────────────┘
```

## Docker Setup

### docker-compose.yml (Production)

```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "9000:9000"
    volumes:
      - .:/var/www/html
    depends_on:
      - postgres
      - redis
      - meilisearch
    environment:
      - APP_ENV=production

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
      - ./ssl:/etc/nginx/ssl

  reverb:
    build: .
    command: php artisan reverb:start --host=0.0.0.0 --port=8080
    ports:
      - "8080:8080"

  horizon:
    build: .
    command: php artisan horizon

  scheduler:
    build: .
    command: php artisan schedule:work

  postgres:
    image: postgres:16-alpine
    volumes:
      - pgdata:/var/lib/postgresql/data
    environment:
      POSTGRES_DB: aukcije
      POSTGRES_USER: aukcije
      POSTGRES_PASSWORD: ${DB_PASSWORD}

  redis:
    image: redis:7-alpine
    volumes:
      - redisdata:/data

  meilisearch:
    image: getmeili/meilisearch:v1
    volumes:
      - msdata:/meili_data
    environment:
      MEILI_MASTER_KEY: ${MEILI_KEY}

volumes:
  pgdata:
  redisdata:
  msdata:
```

## Hosting Options

| Opcija | Specifikacija | Cijena | Napomene |
|--------|-------------|--------|----------|
| **AWS Frankfurt** | t3.medium (2 vCPU, 4GB) | ~$35/mj | Najniži latency ka EX-YU |
| **DigitalOcean Frankfurt** | Premium Droplet (2 vCPU, 4GB) | ~$28/mj | Jednostavniji management |
| **Hetzner Falkenstein** | CPX21 (3 vCPU, 4GB) | ~$8/mj | Najjeftiniji, dobar perf |

**Preporuka:** Hetzner za MVP (cijena), migracija na AWS za skaliranje.

## Nginx Configuration

```nginx
server {
    listen 443 ssl http2;
    server_name aukcije.ba;

    ssl_certificate /etc/nginx/ssl/fullchain.pem;
    ssl_certificate_key /etc/nginx/ssl/privkey.pem;

    root /var/www/html/public;
    index index.php;

    # Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static assets caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # WebSocket proxy
    location /app {
        proxy_pass http://reverb:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
    }
}
```

## CI/CD Pipeline (GitHub Actions)

```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-dev
      - run: php artisan test

  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to server
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: deploy
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /var/www/aukcije
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan queue:restart
            php artisan reverb:restart
```

## Backup Strategy

| Šta | Kako | Frekvencija |
|-----|------|-------------|
| PostgreSQL | pg_dump → S3 | Svaki dan (00:00) |
| Redis (AOF) | Copy to S3 | Svaka 6h |
| Uploaded images | Već na S3 | N/A |
| Application code | Git | Na svaki push |

## SSL/TLS

- **Provider:** Let's Encrypt (Certbot)
- **Auto-renewal:** Cron job svakih 60 dana
- **Cloudflare:** Full (strict) SSL mode
- **HSTS:** Enabled (max-age=31536000)

## Monitoring

| Alat | Svrha |
|------|-------|
| Laravel Horizon | Queue monitoring dashboard |
| Laravel Telescope | Debug + request monitoring (dev/staging) |
| UptimeRobot | Uptime monitoring + alerting |
| Sentry | Error tracking (production) |
| Cloudflare Analytics | Traffic + security analytics |
