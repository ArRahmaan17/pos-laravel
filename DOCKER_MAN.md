# Docker Management Manual (Docker Man)

This project is fully dockerized using **FrankenPHP** and **Docker Compose**. It provides a high-performance, containerized environment for both development and production.

## 🚀 Getting Started

To start the entire environment:

```bash
docker-compose up -d --build
```

This will spin up:
- **pos-app**: The Laravel application running on FrankenPHP (also hosts **Laravel Reverb**).
- **pos-nginx**: Nginx acting as a high-performance reverse proxy.
- **pos-mysql**: MySQL 8.0 database.
- **pos-redis**: Redis for caching, sessions, and real-time data.

## 🛠️ Automatic Features

The Docker setup includes **Auto-Initialization** logic in `build/entrypoint.sh`:
1.  **Database Check**: On startup, it verifies if the database exists via `application/database/ensure_db.php`.
2.  **Migrations & Seeding**: Runs `php artisan migrate:fresh --seed` automatically (wipes and re-seeds the database).
3.  **Optimization**: Automatically runs `config:cache`, `route:cache`, `view:cache`, and `event:cache`.
4.  **Integrated Services**: Starts **Laravel Reverb** on port 8001 within the app container.

## 📁 Key Files

- `build/Dockerfile`: Multi-stage build (Node -> Composer -> FrankenPHP).
- `docker-compose.yml`: Service orchestration.
- `build/entrypoint.sh`: Startup script logic.
- `application/database/ensure_db.php`: Database existence helper.
- `build/nginx/default.conf`: Nginx reverse proxy configuration.

## 🔍 Commands

### Viewing Logs
```bash
# All services
docker-compose logs -f

# App only
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

