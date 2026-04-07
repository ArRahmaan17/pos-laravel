# DPOS - Point of Sale System

DPOS is a multi-company POS application built with Laravel, FrankenPHP, MySQL, Redis, and Vite. The repository is structured to run primarily through Docker Compose for both development and production-style deployments.

## Features

- Multi-company POS workflow
- Product, warehouse, and stock management
- Sales, discounts, payments, and receipts
- Role and permission management
- Reporting and export support
- Realtime features through Laravel Reverb

## Current Stack

- Laravel 11
- PHP 8.2+ at the application level
- FrankenPHP `php8.4-alpine` in Docker
- MySQL 8.0
- Redis 7
- Vite 7
- Tailwind via Vite

## Requirements

### Recommended

- Docker Engine
- Docker Compose plugin (`docker compose`)

### Without Docker

- PHP 8.2+
- Composer
- Node.js 22+
- MySQL 8.0+
- Redis 7+

## Repository Layout

```text
.
├── application/              # Laravel application
├── build/
│   ├── Dockerfile            # Default app image build
│   ├── Dockerfile.multi-pm   # App image build with npm/yarn/pnpm lockfile support
│   ├── entrypoint.sh         # Container startup logic
│   ├── nginx/default.conf    # Nginx reverse proxy config
│   └── php/
│       ├── opcache.dev.ini   # Dev PHP cache settings
│       └── opcache.ini       # Production PHP cache settings
├── docker-compose.yml        # Development stack
├── docker-compose.prod.yml   # Production-oriented stack
├── .env.example              # Root compose environment template
└── README.md
```

## Environment Variables

The root `.env` file is consumed by Docker Compose.

Create it from `.env.example`:

```bash
cp .env.example .env
```

Example local values:

```env
DB_CONNECTION=mysql
DB_PORT=3306
DB_FORWARD_PORT=3307
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
APP_PATH=
NGINX_APP_PORT=80
APP_PORT=80
REDIS_FORWARD_PORT=6380
REVERB_CONNECTION=app
REVERB_PORT=8001
APP_ENV=local
```

Important variables:

- `APP_PORT`: host port exposed by Nginx
- `DB_FORWARD_PORT`: host port exposed by MySQL in development
- `REDIS_FORWARD_PORT`: host port exposed by Redis in development
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: database credentials used by both app and MySQL services

## Development

Start the local stack:

```bash
docker compose up -d --build
```

Open:

```text
http://127.0.0.1
```

Development stack behavior in [`docker-compose.yml`](docker-compose.yml):

- `app` is built from [`build/Dockerfile`](build/Dockerfile)
- `application/` is bind-mounted into the container
- `vendor`, `node_modules`, and `public/build` are stored in named volumes
- PHP opcache is overridden with [`build/php/opcache.dev.ini`](build/php/opcache.dev.ini) so PHP file changes are revalidated
- MySQL is exposed on `DB_FORWARD_PORT`
- Redis is exposed on `REDIS_FORWARD_PORT`

### Important Development Behavior

The app entrypoint in [`build/entrypoint.sh`](build/entrypoint.sh) currently does the following on container start:

- ensures the database exists
- clears Laravel caches
- runs `php artisan migrate:fresh --seed` when `APP_ENV` is not `production`
- starts FrankenPHP and Laravel Reverb

This means restarting the `app` container in development resets the database.

### Live Code Changes

PHP file changes should be reflected without rebuilding because development opcache timestamp validation is enabled.

If changes are not visible:

```bash
docker compose up -d --force-recreate app nginx
docker compose exec app frankenphp php-cli artisan optimize:clear
```

### Frontend Assets

Vite assets are expected at `application/public/build`.

Current Docker behavior:

- assets are built into the image during Docker build
- the development stack keeps `public/build` in a named volume
- the app container itself is not a Node runtime for day-to-day asset compilation

If the Vite manifest is missing, rebuild the app image:

```bash
docker compose up -d --build app
```

## Production

The production-oriented stack is defined in [`docker-compose.prod.yml`](docker-compose.prod.yml).

Start it with a dedicated production env file:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml up -d --build
```

Production stack behavior:

- `app` is built from [`build/Dockerfile.multi-pm`](build/Dockerfile.multi-pm)
- no application source bind mount is used
- MySQL and Redis use named volumes only
- only the web port is published
- `APP_ENV=production` and `APP_DEBUG=false` are forced in Compose
- entrypoint runs `php artisan migrate --force` in production

Do not reuse the local `.env` for production. Use a separate file such as `.env.production`.

## Common Commands

View service status:

```bash
docker compose ps
```

View logs:

```bash
docker compose logs -f
docker compose logs -f app
docker compose logs -f nginx
```

Run Artisan commands:

```bash
docker compose exec app frankenphp php-cli artisan about
docker compose exec app frankenphp php-cli artisan optimize:clear
```

Open a shell in the app container:

```bash
docker compose exec app sh
```

Open MySQL:

```bash
docker compose exec mysql mysql -u root -p
```

## Testing

Run tests inside the app container:

```bash
docker compose exec app frankenphp php-cli artisan test
```

Run Pint:

```bash
docker compose exec app ./vendor/bin/pint
```

## Troubleshooting

### `/auth/login` or other pages return `500`

Check that the Vite manifest exists:

```bash
docker compose exec app sh -lc 'ls -la /var/www/html/public/build && test -f /var/www/html/public/build/manifest.json && echo ok'
```

If it is missing, rebuild the app image:

```bash
docker compose up -d --build app
```

### Code changes do not appear

Recreate the app and clear Laravel caches:

```bash
docker compose up -d --force-recreate app nginx
docker compose exec app frankenphp php-cli artisan optimize:clear
```

### Database was unexpectedly reset

This is current development behavior. The app entrypoint runs:

```bash
php artisan migrate:fresh --seed
```

when `APP_ENV` is not `production`.

### Check Laravel logs

```bash
docker compose exec app sh -lc 'tail -f storage/logs/laravel.log'
```

## Notes

- Use `docker compose`, not the legacy `docker-compose` command.
- The development and production compose files are intentionally different.
- The production stack should use a separate env file and deployment-specific secrets.

## License

This software is licensed under the Academic-Commercial Dual License. See [LICENSE](LICENSE) for the full terms.

For commercial licensing:

- Email: `ardrah17@gmail.com`

## Contact

- Maintainer: Ardhi Rahmaan MS
- GitHub: <https://github.com/ArRahmaan17>
- Website: <https://dpos.rahmaanms.my.id>
