# DPOS - Point of Sale System

DPOS is a multi-company POS application built with Laravel 12, FrankenPHP, MySQL, Redis, Reverb, and Vite.

## Requirements

Development requires only Docker Engine and the Docker Compose plugin. PHP, Composer, and Node.js run inside containers.

## Development

Create the Compose environment file:

```bash
cp .env.example .env
```

At minimum, set the database credentials and local ports. A typical configuration is:

```env
DB_PORT=3306
DB_FORWARD_PORT=3307
DB_DATABASE=dpos
DB_USERNAME=dpos
DB_PASSWORD=pospassword
APP_PORT=8020
VITE_PORT=5173
REDIS_FORWARD_PORT=6381
REVERB_PORT=81
APP_ENV=local
RUN_DB_INIT=true
RUN_MIGRATIONS=true
SEED_DB_ON_BOOT=false
```

The development Compose file always disables automatic database resets. Run
`docker compose exec app frankenphp php-cli artisan migrate:fresh --seed`
explicitly when a destructive reset is intended.

Start the stack:

```bash
docker compose up --build
```

Open <http://127.0.0.1:8020>. Vite HMR is exposed on port `5173`, and Reverb is exposed on port `81`.

The development stack is intentionally split by responsibility:

- `app` runs Laravel through FrankenPHP.
- `vite` runs the frontend development server with HMR.
- `reverb` runs the WebSocket server independently.
- `mysql` and `redis` provide local persistence.
- Composer and npm dependencies live in Docker volumes, not on the host.
- Dependency installs run only when `composer.json` or `package.json` changes.
- Download caches persist across container rebuilds.
- PHP opcache revalidates source files immediately.

The project intentionally does not use `composer.lock` or `package-lock.json`. Dependency versions are resolved from the constraints in `composer.json` and `package.json`.

### Common commands

```bash
# Status and logs
docker compose ps
docker compose logs -f app vite reverb

# Artisan
docker compose exec app frankenphp php-cli artisan about
docker compose exec app frankenphp php-cli artisan migrate
docker compose exec app frankenphp php-cli artisan test

# Code formatting
docker compose exec app ./vendor/bin/pint

# Shells
docker compose exec app sh
docker compose exec mysql mysql -u root -p
```

When PHP dependencies change, restart `app`; when frontend dependencies change, restart `vite`:

```bash
docker compose restart app
docker compose restart vite
```

To force a clean dependency reinstall without touching MySQL data:

```bash
docker compose down
docker volume rm pos-laravel_vendor_data pos-laravel_node_modules_data
docker compose up --build
```

Do not use `docker compose down -v` unless deleting the development database is intended.

## Production

The production-oriented stack remains in `docker-compose.prod.yml`:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml up -d --build
```

Use deployment-specific secrets and keep `RESET_DB_ON_BOOT=false` in production.

## Repository layout

```text
application/                 Laravel application
build/Dockerfile             Development and production image targets
build/entrypoint.sh          PHP dependency and application startup logic
build/frontend-entrypoint.sh Node dependency and Vite startup logic
build/php/                    Development and production opcache settings
docker-compose.yml           Development stack
docker-compose.prod.yml      Production-oriented stack
```

## License

This software is licensed under the Academic-Commercial Dual License. See [LICENSE](LICENSE) for the full terms.

For commercial licensing, contact `ardrah17@gmail.com`.
