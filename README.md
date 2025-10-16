# Backend — API-only Laravel service

This repository is the backend API for the Valorant Skin Collector project. It's intentionally API-focused (no frontend) and implements user authentication, a cached proxy for the Valorant API, and endpoints to manage a user's skin collection and wishlist.

## What this service provides
-   Cookie-based SPA authentication using Laravel Sanctum
-   Social auth (Google / GitHub) integration stubs using Laravel Socialite
-   `GET /api/v1/skins` — cached proxy to Valorant API (Redis-backed cache)
-   Authenticated CRUD endpoints for user skins (collection + wishlist)
-   DB migrations and simple models for persisting user collections

## Quick start (local)

1. Install dependencies and copy env:

```bash
cd valorant-skin-collector-backend
cp .env.example .env
composer install
php artisan key:generate
```

2. Edit `.env`

-   Set `DB_*` values (database, user, password)
-   If using Redis locally, set `REDIS_HOST` (default `127.0.0.1`)
-   Optionally add social provider keys in `config/services.php` or `.env`

3. Run migrations:

```bash
php artisan migrate --seed
```

4. Run the dev server:

```bash
php artisan serve
# Server will be at http://127.0.0.1:8000 by default
```

5. (Optional) Start Redis locally with Docker:

```bash
docker run -p 6379:6379 --name redis -d redis:7
```

## Configuration notes

-   Routes are versioned under `routes/api.php` and prefixed with `/api/v1`.
-   The backend caches Valorant responses in Redis for performance. If Redis is not available, the service will still try to fetch directly from the upstream API.
-   The project includes migrations to add social columns (`provider`, `provider_id`) to `users` to support Socialite.

## Useful commands

-   Run migrations: `php artisan migrate`
-   Run tests: `vendor/bin/phpunit`
-   Clear caches: `php artisan config:clear && php artisan route:clear`

## Endpoints (summary)

-   GET `/api/v1/skins` — public, returns cached skin list (accepts `perPage` and `page`)
-   POST `/api/v1/auth/login`, POST `/api/v1/auth/logout`, GET `/api/v1/auth/me` — authentication
-   GET/POST/DELETE `/api/v1/user/skins` — manage authenticated user's collection
-   Social redirects/callbacks: `/api/v1/auth/social/redirect/{provider}` and `/api/v1/auth/social/callback/{provider}`

### Disclaimer

Valorant is a trademark of Riot Games. This project is for educational/personal use and is not endorsed by or affiliated with Riot Games.
