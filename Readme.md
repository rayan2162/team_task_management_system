# TaskFlow — Team Task Management System

Full-stack project management application built with **Laravel 12** (backend) and **React 19 + TypeScript** (frontend).

## Tech Stack

| Layer    | Technology                                  |
| -------- | ------------------------------------------- |
| Backend  | Laravel 12, PHP 8.2, Sanctum, PostgreSQL    |
| Frontend | React 19, TypeScript, Vite 8, Tailwind CSS 4 |
| State    | Zustand, TanStack React Query               |
| Infra    | Docker, Nginx, Redis                        |

## Quick Start

```bash
# Backend
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve

# Frontend (separate terminal)
cd frontend
npm install
npm run dev
```

The frontend dev server runs on `http://localhost:5173` and proxies API calls to `http://localhost:8000`.

## Docker

```bash
docker compose up --build
```

| Service  | Port |
| -------- | ---- |
| Backend  | 8000 |
| Frontend | 3000 |
| Redis    | 6379 |

## Testing

```bash
cd backend
php artisan test
# 48 tests, 116 assertions
```

## API Documentation

### OpenAPI / Swagger Spec

The full OpenAPI 3.1 specification is at [`openapi.yaml`](openapi.yaml).

**View interactively:**

```bash
# Option 1 — Swagger UI via Docker
docker run -p 8080:8080 -e SWAGGER_JSON=/spec/openapi.yaml -v $(pwd):/spec swaggerapi/swagger-ui
# Then open http://localhost:8080

# Option 2 — Swagger Editor (online)
# Go to https://editor.swagger.io and import openapi.yaml
```

**Validate the spec:**

```bash
npx @redocly/cli lint openapi.yaml
```

### Postman Collection

1. Open Postman → **Import**
2. Select `postman_collection.json`
3. Import `postman_environment.json` via **Environments → Import**
4. Select the **TaskFlow — Local** environment
5. Run **Login** first — the `auth_token` variable is auto-set from the response
6. All subsequent requests use `{{auth_token}}` automatically

**Environment variables:**

| Variable      | Description                    |
| ------------- | ------------------------------ |
| `base_url`    | API base URL                   |
| `auth_token`  | Bearer token (auto-set)        |
| `project_id`  | Current project ID (auto-set)  |
| `task_id`     | Current task ID (auto-set)     |
| `subtask_id`  | Current subtask ID (auto-set)  |

### Endpoint Summary

| Group     | Endpoints |
| --------- | --------- |
| Health    | 1         |
| Auth      | 4         |
| Projects  | 5         |
| Tasks     | 6         |
| Subtasks  | 5         |
| Dashboard | 2         |
| Profile   | 3         |
| **Total** | **26**    |
