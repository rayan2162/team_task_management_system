# TaskFlow Backend — Laravel 12 REST API

The backend for the TaskFlow team task management system. A RESTful JSON API built with **Laravel 12**, **PHP 8.2**, **Laravel Sanctum** for token authentication, and **PostgreSQL** as the database.

---

## Table of Contents

- [Architecture](#architecture)
- [Directory Structure](#directory-structure)
- [Setup](#setup)
- [Environment Variables](#environment-variables)
- [Database](#database)
- [API Endpoints](#api-endpoints)
- [Authentication](#authentication)
- [Authorisation Policies](#authorisation-policies)
- [Domain Layer (DDD-Lite)](#domain-layer-ddd-lite)
- [Models & Relationships](#models--relationships)
- [Form Requests & Validation](#form-requests--validation)
- [API Resources](#api-resources)
- [Response Format](#response-format)
- [Testing](#testing)
- [Seeder & Demo Data](#seeder--demo-data)
- [Docker](#docker)

---

## Architecture

The backend follows a **DDD-lite** (Domain-Driven Design lite) pattern:

```
Request
  → Route (routes/api.php)
    → Controller (thin — delegates to service)
      → Form Request (validation)
      → Policy (authorisation)
      → Domain Service (business logic)
        → Repository (data access)
          → Eloquent Model (ORM)
      → API Resource (response shaping)
        → JSON Response (standardised envelope)
```

**Key principles:**
- Controllers are thin — they only validate, authorise, and delegate.
- Business logic lives in Domain Services.
- Data access is abstracted through Repositories.
- Validation rules are in dedicated Form Request classes.
- Authorisation rules are in Policy classes.
- Response transformation is handled by API Resource classes.
- A shared `ApiResponse` trait provides a consistent JSON envelope.

---

## Directory Structure

```
app/
├── Domain/                          ← Business logic layer
│   ├── Auth/Services/
│   │   └── AuthService.php          ← Register, login, logout
│   ├── Dashboard/Services/
│   │   └── DashboardService.php     ← Stats & analytics queries
│   ├── Profile/Services/
│   │   └── ProfileService.php       ← Profile update, avatar upload
│   ├── Project/
│   │   ├── Repositories/
│   │   │   └── ProjectRepository.php
│   │   └── Services/
│   │       └── ProjectService.php   ← Create, join, archive, list
│   ├── Subtask/
│   │   ├── Repositories/
│   │   │   └── SubtaskRepository.php
│   │   └── Services/
│   │       └── SubtaskService.php
│   └── Task/
│       ├── Repositories/
│       │   └── TaskRepository.php
│       └── Services/
│           └── TaskService.php
│
├── Http/
│   ├── Controllers/Api/V1/         ← Versioned API controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProfileController.php
│   │   ├── ProjectController.php
│   │   ├── SubtaskController.php
│   │   └── TaskController.php
│   │
│   ├── Requests/                   ← Form Request validation
│   │   ├── Auth/
│   │   │   ├── LoginRequest.php
│   │   │   └── RegisterRequest.php
│   │   ├── Profile/
│   │   │   └── UpdateProfileRequest.php
│   │   ├── Project/
│   │   │   ├── JoinProjectRequest.php
│   │   │   └── StoreProjectRequest.php
│   │   ├── Subtask/
│   │   │   ├── StoreSubtaskRequest.php
│   │   │   ├── UpdateSubtaskRequest.php
│   │   │   └── UpdateSubtaskStatusRequest.php
│   │   └── Task/
│   │       ├── StoreTaskRequest.php
│   │       ├── UpdateTaskRequest.php
│   │       └── UpdateTaskStatusRequest.php
│   │
│   └── Resources/                  ← API Resource transformers
│       ├── ProjectResource.php
│       ├── SubtaskResource.php
│       ├── TaskResource.php
│       └── UserResource.php
│
├── Models/                         ← Eloquent models
│   ├── Project.php
│   ├── Subtask.php
│   ├── Task.php
│   └── User.php
│
├── Policies/                       ← Authorisation policies
│   ├── ProjectPolicy.php
│   ├── SubtaskPolicy.php
│   └── TaskPolicy.php
│
├── Providers/
│   └── AppServiceProvider.php
│
└── Traits/
    └── ApiResponse.php             ← Standardised JSON responses

database/
├── factories/
│   └── UserFactory.php
├── migrations/                     ← 8 migration files
│   ├── create_users_table
│   ├── create_cache_table
│   ├── create_jobs_table
│   ├── create_personal_access_tokens_table
│   ├── create_projects_table
│   ├── create_project_members_table
│   ├── create_tasks_table
│   └── create_subtasks_table
└── seeders/
    └── DatabaseSeeder.php          ← Demo data

routes/
└── api.php                         ← All 26 endpoints

tests/
├── Pest.php                        ← Pest config (RefreshDatabase)
├── TestCase.php
└── Feature/
    ├── Auth/                       ← RegisterTest, LoginTest, LogoutTest
    ├── Dashboard/                  ← DashboardTest
    ├── Profile/                    ← ProfileTest
    ├── Project/                    ← CreateProjectTest, JoinProjectTest, ProjectCodeTest
    ├── Subtask/                    ← SubtaskTest
    ├── Task/                       ← TaskTest
    └── HealthTest.php
```

---

## Setup

### Prerequisites

- PHP 8.2+ with extensions: `pdo_pgsql`, `mbstring`, `xml`, `zip`, `bcmath`
- Composer 2.x
- PostgreSQL 15+

### Installation

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

The API is now available at `http://localhost:8000/api`.

---

## Environment Variables

Key `.env` values:

| Variable | Description | Example |
| --- | --- | --- |
| `DB_CONNECTION` | Database driver | `pgsql` |
| `DB_HOST` | Database host | `localhost` |
| `DB_PORT` | Database port | `5432` |
| `DB_DATABASE` | Database name | `team-task-management-system` |
| `DB_USERNAME` | Database user | `postgres` |
| `DB_PASSWORD` | Database password | `secret` |
| `DB_SSLMODE` | SSL mode (NeonDB requires `require`) | `prefer` |
| `APP_KEY` | Application encryption key | _(auto-generated)_ |
| `FILESYSTEM_DISK` | File storage disk | `local` |

---

## Database

### Tables

| Table | Purpose |
| --- | --- |
| `users` | User accounts (name, email, password, avatar) |
| `projects` | Projects (code, name, status, created_by) |
| `project_members` | Pivot table — user ↔ project membership |
| `tasks` | Tasks (title, status, deadline, assigned_to, project_id) |
| `subtasks` | Subtasks (body, status, deadline, assigned_to, task_id) |
| `personal_access_tokens` | Sanctum tokens |
| `cache` | Laravel cache store |
| `jobs` / `job_batches` / `failed_jobs` | Queue tables |

### Status Enums

- **Task/Subtask status:** `pending`, `working`, `done`
- **Project status:** `active`, `archived`, `done`

### Key Constraints

- `projects.code` — `VARCHAR(20) UNIQUE`
- `project_members` — `UNIQUE(project_id, user_id)`
- `tasks.project_id` — `FK → projects.id ON DELETE CASCADE`
- `tasks.assigned_to` — `FK → users.id ON DELETE SET NULL`
- `subtasks.task_id` — `FK → tasks.id ON DELETE CASCADE`

---

## API Endpoints

All endpoints are prefixed with `/api`. Protected endpoints require `Authorization: Bearer <token>`.

### Health (1)

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/health` | Server health check |

### Auth (4)

| Method | Path | Description |
| --- | --- | --- |
| `POST` | `/v1/auth/register` | Register a new user |
| `POST` | `/v1/auth/login` | Login and receive token |
| `POST` | `/v1/auth/logout` | Revoke current token |
| `GET` | `/v1/auth/me` | Get current user |

### Projects (5)

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/v1/projects` | List user's projects |
| `POST` | `/v1/projects` | Create project |
| `GET` | `/v1/projects/{id}` | Get project with members/tasks/subtasks |
| `POST` | `/v1/projects/join` | Join by project code |
| `PATCH` | `/v1/projects/{id}/archive` | Archive project (creator only) |

### Tasks (6)

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/v1/projects/{id}/tasks` | List tasks (filterable by `?status=` `?assigned_to=`) |
| `POST` | `/v1/projects/{id}/tasks` | Create task |
| `GET` | `/v1/tasks/{id}` | Get task with subtasks |
| `PUT` | `/v1/tasks/{id}` | Update task |
| `DELETE` | `/v1/tasks/{id}` | Delete task |
| `PATCH` | `/v1/tasks/{id}/status` | Update status only |

### Subtasks (5)

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/v1/tasks/{id}/subtasks` | List subtasks for a task |
| `POST` | `/v1/tasks/{id}/subtasks` | Create subtask |
| `PUT` | `/v1/subtasks/{id}` | Update subtask |
| `DELETE` | `/v1/subtasks/{id}` | Delete subtask |
| `PATCH` | `/v1/subtasks/{id}/status` | Update status (assignee/creator only) |

### Dashboard (2)

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/v1/dashboard` | Stats: assigned tasks/subtasks, completed, rate |
| `GET` | `/v1/dashboard/analytics` | 30-day daily completion counts |

### Profile (3)

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/v1/profile` | Get profile |
| `PATCH` | `/v1/profile` | Update name/email |
| `POST` | `/v1/profile/avatar` | Upload avatar (multipart, max 2 MB) |

---

## Authentication

**Laravel Sanctum** token-based auth:

1. **Register** (`POST /v1/auth/register`) — creates user + issues token.
2. **Login** (`POST /v1/auth/login`) — validates credentials + issues token.
3. **Protected routes** — send `Authorization: Bearer <token>` header.
4. **Logout** (`POST /v1/auth/logout`) — deletes the current token.

Tokens are stored in the `personal_access_tokens` table and scoped per-device.

---

## Authorisation Policies

| Policy | Method | Rule |
| --- | --- | --- |
| `ProjectPolicy` | `view`, `createTask` | User is a member of the project |
| `ProjectPolicy` | `manage` | User is the project creator (`created_by`) |
| `TaskPolicy` | `view`, `update`, `delete` | User is a member of the task's project |
| `SubtaskPolicy` | `view`, `update`, `delete` | User is a member of the subtask's task's project |
| `SubtaskPolicy` | `updateStatus` | User is the subtask's `assigned_to` **OR** the project's `created_by` |

Policies are enforced via `Gate::authorize()` in controllers.

---

## Domain Layer (DDD-Lite)

### Services

| Service | Responsibilities |
| --- | --- |
| `AuthService` | Register (create user + token), login (validate + token), logout (revoke token) |
| `ProjectService` | List user projects, create (auto-generate code + add creator as member), show (eager load), join (validate code, prevent duplicates), archive |
| `TaskService` | List by project (with filters), show (with subtasks), create, update, update status, delete |
| `SubtaskService` | List by task, create, update, update status, delete |
| `DashboardService` | `getStats()` — assigned tasks/subtasks counts, completion rate; `getAnalytics()` — GROUP BY DATE for last 30 days |
| `ProfileService` | Update name/email; upload avatar (store file, delete old, update user) |

### Repositories

| Repository | Methods |
| --- | --- |
| `ProjectRepository` | `getUserProjects`, `findWithDetails`, `findByCode`, `isMember`, `addMember`, `create` |
| `TaskRepository` | `getByProject` (with filters), `findWithSubtasks`, `create`, `update`, `delete` |
| `SubtaskRepository` | `getByTask`, `create`, `update`, `delete` |

---

## Models & Relationships

### User

| Relationship | Type | Target |
| --- | --- | --- |
| `createdProjects()` | hasMany | Project |
| `projects()` | belongsToMany | Project (via `project_members`) |
| `assignedTasks()` | hasMany | Task |
| `assignedSubtasks()` | hasMany | Subtask |

### Project

| Relationship | Type | Target |
| --- | --- | --- |
| `creator()` | belongsTo | User |
| `members()` | belongsToMany | User (via `project_members`) |
| `tasks()` | hasMany | Task |

Auto-generates a unique code (`PRJ` + 6 random uppercase alphanumeric) via the `booted()` static method.

### Task

| Relationship | Type | Target |
| --- | --- | --- |
| `project()` | belongsTo | Project |
| `creator()` | belongsTo | User |
| `assignee()` | belongsTo | User |
| `subtasks()` | hasMany | Subtask |

### Subtask

| Relationship | Type | Target |
| --- | --- | --- |
| `task()` | belongsTo | Task |
| `assignee()` | belongsTo | User |

---

## Form Requests & Validation

| Request | Rules |
| --- | --- |
| `RegisterRequest` | `name`: required, max:100 · `email`: required, email, unique · `password`: required, min:8, confirmed |
| `LoginRequest` | `email`: required, email · `password`: required |
| `StoreProjectRequest` | `name`: required, max:150 |
| `JoinProjectRequest` | `code`: required, string |
| `StoreTaskRequest` | `title`: required, max:150 · `status`: sometimes, in:pending/working/done · `deadline`: nullable, date · `assigned_to`: nullable, exists:users |
| `UpdateTaskRequest` | Same as StoreTask but all fields `sometimes` |
| `UpdateTaskStatusRequest` | `status`: required, in:pending/working/done |
| `StoreSubtaskRequest` | `body`: required · `status`: sometimes, in:... · `deadline`: nullable, date · `assigned_to`: nullable, exists:users |
| `UpdateSubtaskRequest` | Same as StoreSubtask but all fields `sometimes` |
| `UpdateSubtaskStatusRequest` | `status`: required, in:pending/working/done |
| `UpdateProfileRequest` | `name`: sometimes, max:100 · `email`: sometimes, email, unique (excluding current user) |

---

## API Resources

| Resource | Fields |
| --- | --- |
| `UserResource` | `id`, `name`, `email`, `avatar` (full URL or null), `created_at` |
| `ProjectResource` | `id`, `code`, `name`, `status`, `created_by`, `creator` (whenLoaded), `members` (whenLoaded), `tasks` (whenLoaded), `created_at`, `updated_at` |
| `TaskResource` | `id`, `project_id`, `title`, `status`, `deadline`, `created_by`, `assigned_to`, `creator`, `assignee`, `project`, `subtasks` (all whenLoaded), `created_at`, `updated_at` |
| `SubtaskResource` | `id`, `task_id`, `body`, `status`, `deadline`, `assigned_to`, `assignee` (whenLoaded), `created_at`, `updated_at` |

---

## Response Format

All responses use the `ApiResponse` trait:

**Success:**

```json
{
  "success": true,
  "message": "Task created successfully.",
  "data": { ... }
}
```

**Error:**

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

**Status codes used:** `200`, `201`, `400`, `401`, `403`, `404`, `422`, `500`

**Exception handling** in `bootstrap/app.php`:

| Exception | Response |
| --- | --- |
| `AuthenticationException` | 401 — Unauthenticated. |
| `ValidationException` | 422 — Validation failed + errors object |
| `NotFoundHttpException` | 404 — Resource not found. |
| `AccessDeniedHttpException` | 403 — This action is unauthorized. |

---

## Testing

```bash
php artisan test
```

**48 tests · 116 assertions**

| Suite | Tests | What's Covered |
| --- | --- | --- |
| `Auth/RegisterTest` | 4 | Valid registration, validation errors, duplicate email, password confirmation |
| `Auth/LoginTest` | 4 | Valid login, wrong password, non-existent email, validation |
| `Auth/LogoutTest` | 3 | Authenticated logout, unauthenticated rejection, get current user |
| `Project/CreateProjectTest` | 4 | Create project, validation, auto-code generation, creator becomes member |
| `Project/JoinProjectTest` | 5 | Join by code, already-member error, invalid code, duplicate constraint |
| `Project/ProjectCodeTest` | 3 | Code format (PRJ prefix + 6 chars), uniqueness guarantee |
| `Task/TaskTest` | 9 | CRUD operations, status update, non-member rejection, invalid status, list with filters |
| `Subtask/SubtaskTest` | 8 | CRUD, assignee status update, creator status update, non-assignee/creator rejection |
| `Dashboard/DashboardTest` | 3 | Stats retrieval, analytics data, unauthenticated rejection |
| `Profile/ProfileTest` | 4 | Get profile, update fields, email uniqueness, avatar upload |
| `HealthTest` | 1 | Returns `{ "status": "ok" }` |

Tests use **SQLite in-memory** for fast, isolated execution.

---

## Seeder & Demo Data

`php artisan db:seed` creates:

| Entity | Data |
| --- | --- |
| **Users** | Rayan (`rayan@example.com` / `password`) + Team Member (`member@example.com` / `password`) |
| **Projects** | Cashflow App (Issues), UI/UX Design, Devops Pipeline, Backend API, Frontend App |
| **Tasks** | 2 tasks in the main project (pending + working) |
| **Subtasks** | 3 subtasks under the first task |

Both users are members of all projects.

---

## Docker

```dockerfile
# Dockerfile — PHP 8.2-FPM
FROM php:8.2-fpm
# Installs pdo_pgsql, mbstring, xml, zip, bcmath, redis
# Copies Composer deps, application code
# Optimises autoloader, clears config cache
```

Used by `docker-compose.yml` at the project root. The Nginx service proxies requests to `backend:9000` (PHP-FPM).
