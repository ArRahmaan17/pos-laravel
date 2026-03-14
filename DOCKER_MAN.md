# Docker Management Manual (Docker Man)

This project is fully dockerized using **FrankenPHP** and **Docker Compose**.

## 🚀 Getting Started

To start the entire environment:

```bash
docker-compose up -d --build
```

This will spin up:
- **pos-app**: The Laravel application running on FrankenPHP.
- **pos-worker**: A dedicated queue worker.
- **pos-reverb**: Laravel Reverb for real-time capabilities.
- **pos-mysql**: MySQL 8.0 database.
- **pos-redis**: Redis for caching and queues.
- **pos-nginx**: Nginx as a reverse proxy/static asset server.

## 🛠️ Automatic Features

The Docker setup now includes **Auto-Initialization**:
1.  **Database Creation**: On startup, the container checks if the database specified in `.env` exists. If not, it creates it.
2.  **User Privileges**: If connected as root, it ensures the configured `DB_USERNAME` has full access to the database.
3.  **Migrations**: `php artisan migrate --force` runs automatically on every app container start.
4.  **Caching**: Configuration, routes, and views are automatically cached for production performance.

## 📁 Key Files

- `Dockerfile`: Multi-stage build (Node -> Composer -> FrankenPHP).
- `docker-compose.yml`: Service orchestration.
- `buildentrypoint.sh`: Startup script logic.
- `database/ensure_db.php`: Database existence helper.

## 🔍 Commands

### Viewing Logs
```bash
docker-compose logs -f app
```

### Running Artisan Commands
```bash
docker-compose exec app php artisan [command]
```

### Accessing Database
```bash
docker-compose exec mysql mysql -u root -p
```

### Rebuilding After Changes
```bash
docker-compose up -d --build
```
