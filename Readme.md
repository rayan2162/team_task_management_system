# Team Task Management System

A production-quality, full-stack project management application designed for teams to create projects, organise tasks with nested subtasks, track progress with real-time dashboards, and collaborate through a project-code-based invite system.

Built with **Laravel 12** on the backend and **React 19 + TypeScript** on the frontend, deployed via **Docker** and backed by **PostgreSQL**.

---

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Docker Deployment](#docker-deployment)
- [Testing](#testing)
- [API Documentation](#api-documentation)
- [Database Design](#database-design)
- [Authentication & Authorisation](#authentication--authorisation)
- [Design Documents](#design-documents)

---

## Overview

TaskFlow is a team task management system where users can:

1. **Register / log in** to their account (token-based auth via Laravel Sanctum).
2. **Create projects** — each project receives a unique auto-generated code (e.g. `PRJX29K41`).
3. **Invite team members** — share the project code; members join by entering it.
4. **Manage tasks** — create, assign, set deadlines, update status (`pending` → `working` → `done`).
5. **Break tasks into subtasks** — each subtask has its own assignee, deadline, and status.
6. **Track progress** — a personal dashboard shows assigned work, completion metrics, and a 30-day productivity chart.
7. **Manage their profile** — update name, email, and upload an avatar.

The application follows a **DDD-lite** (Domain-Driven Design lite) architecture on the backend with clear separation into Domain Services, Repositories, Policies, Form Requests, and API Resources.

---

## Key Features

| Feature | Description |
| --- | --- |
| **Token Authentication** | Laravel Sanctum bearer tokens; register, login, logout, current-user endpoints |
| **Project Management** | Create, view, join by code, archive projects |
| **Task Management** | Full CRUD + dedicated status-update endpoint; filter by status/assignee |
| **Nested Subtasks** | Each task can have multiple subtasks with independent status, assignee, and deadline |
| **Role-Based Authorisation** | Laravel Policies enforce: only members access projects; only assignee/creator can update subtask status |
| **Dashboard & Analytics** | Personal stats (assigned tasks, subtasks, completion rate) + 30-day daily completion chart |
| **Profile & Avatar** | Update name/email; upload avatar image (multipart) with automatic old-avatar cleanup |
| **Auto-Generated Project Codes** | Unique `PRJ` + 6 alphanumeric characters, generated on the server |
| **Standardised API Responses** | Every endpoint returns `{ success, message, data }` (or `{ success, message, errors }` for failures) |
| **Comprehensive Test Suite** | 48 Pest tests covering auth, projects, tasks, subtasks, dashboard, profile, and health |
| **Full API Documentation** | OpenAPI 3.1 spec + Postman collection + environment file |
| **Docker-Ready** | `docker-compose.yml` with backend (PHP-FPM), frontend (Nginx), Nginx API proxy, and Redis |

---

## Tech Stack

| Layer | Technology | Version |
| --- | --- | --- |
| **Backend Framework** | Laravel | 12 |
| **Language** | PHP | 8.2+ |
| **Authentication** | Laravel Sanctum | Token-based |
| **Database** | PostgreSQL | 15+ |
| **Frontend Framework** | React | 19 |
| **Language** | TypeScript | 5.x |
| **Build Tool** | Vite | 8 |
| **CSS** | Tailwind CSS | 4 |
| **State Management** | Zustand | 5.x |
| **Server State** | TanStack React Query | 5.x |
| **Routing** | React Router DOM | 7.x |
| **Charts** | Recharts | 2.x |
| **HTTP Client** | Axios | 1.x |
| **Testing** | Pest (PHP) | 3.x |
| **Containerisation** | Docker + Docker Compose | — |
| **Web Server** | Nginx | Alpine |

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                       Browser                           │
│              React 19 SPA (TypeScript)                   │
│     Zustand · React Query · React Router · Axios        │
└──────────────────────┬──────────────────────────────────┘
                       │  HTTP (JSON)
                       ▼
┌─────────────────────────────────────────────────────────┐
│                  Nginx Reverse Proxy                     │
│            :8000 → PHP-FPM  |  :3000 → SPA              │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                Laravel 12 Backend                        │
│                                                         │
│  Routes (api.php)                                       │
│    └─ Controllers (thin, delegate to Services)          │
│         ├─ Form Requests (validation)                   │
│         ├─ Policies (authorisation)                     │
│         └─ API Resources (response shaping)             │
│                                                         │
│  Domain Layer (DDD-lite)                                │
│    ├─ Services (business logic)                         │
│    └─ Repositories (data access)                        │
│                                                         │
│  Models (Eloquent ORM)                                  │
│    ├─ User  ├─ Project  ├─ Task  └─ Subtask             │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
              ┌─────────────────┐
              │   PostgreSQL    │
              │   (NeonDB)      │
              └─────────────────┘
```

---

## Project Structure

```
team_task_management_system/
├── Readme.md                       ← You are here
├── openapi.yaml                    ← OpenAPI 3.1 specification
├── postman_collection.json         ← Postman v2.1 collection
├── postman_environment.json        ← Postman environment variables
├── docker-compose.yml              ← Docker Compose (4 services)
├── docker/
│   └── nginx/
│       └── default.conf            ← Nginx config for backend
│
├── backend/                        ← Laravel 12 API
│   ├── app/
│   │   ├── Domain/                 ← DDD-lite: Services & Repositories
│   │   ├── Http/                   ← Controllers, Requests, Resources
│   │   ├── Models/                 ← Eloquent models
│   │   ├── Policies/               ← Authorisation policies
│   │   └── Traits/                 ← ApiResponse trait
│   ├── database/
│   │   ├── migrations/             ← 8 migration files
│   │   ├── factories/              ← UserFactory
│   │   └── seeders/                ← DatabaseSeeder (demo data)
│   ├── routes/
│   │   └── api.php                 ← All 26 API endpoints
│   ├── tests/                      ← 48 Pest tests
│   ├── Dockerfile                  ← PHP 8.2-FPM image
│   └── README.md                   ← Backend documentation
│
├── frontend/                       ← React 19 SPA
│   ├── src/
│   │   ├── api/                    ← Axios API modules
│   │   ├── components/             ← Shared UI (Avatar, Modal, etc.)
│   │   ├── hooks/                  ← React Query hooks
│   │   ├── modules/                ← Feature pages (auth, dashboard, etc.)
│   │   ├── stores/                 ← Zustand auth store
│   │   ├── types/                  ← TypeScript type definitions
│   │   ├── router.tsx              ← React Router config
│   │   ├── App.tsx                 ← Root component
│   │   └── main.tsx                ← Entry point
│   ├── Dockerfile                  ← Multi-stage Node → Nginx build
│   └── README.md                   ← Frontend documentation
│
└── docs/                           ← Design documents
    ├── api_design.md
    ├── DB_Design.md
    ├── MVP_scope_doc.md
    ├── plan.md
    └── system_architecture.md
```

---

## Getting Started

### Prerequisites

- **PHP** 8.2+
- **Composer** 2.x
- **Node.js** 20+
- **npm** 10+
- **PostgreSQL** 15+ (or a NeonDB instance)

### Backend Setup

```bash
cd backend
cp .env.example .env          # Configure DB credentials
composer install
php artisan key:generate
php artisan migrate --seed     # Creates tables + demo data
php artisan storage:link       # Symlink for avatar uploads
php artisan serve              # http://localhost:8000
```

### Frontend Setup

```bash
cd frontend
npm install
npm run dev                    # http://localhost:5173
```

The Vite dev server proxies `/api` requests to `http://localhost:8000` automatically.

### Demo Credentials

After seeding, you can log in with:

| Email | Password |
| --- | --- |
| `rayan@example.com` | `password` |
| `member@example.com` | `password` |

---

## Docker Deployment

```bash
docker compose up --build
```

| Service | Container | Port | Description |
| --- | --- | --- | --- |
| **backend** | `taskflow-backend` | 9000 (internal) | PHP 8.2-FPM with pdo_pgsql, Redis |
| **nginx** | `taskflow-nginx` | **8000** | Reverse proxy → PHP-FPM; serves `/storage` |
| **frontend** | `taskflow-frontend` | **3000** | Production React build served by Nginx |
| **redis** | `taskflow-redis` | 6379 | Cache & session store |

---

## Testing

```bash
cd backend
php artisan test
```

**48 tests · 116 assertions · all passing**

| Test Suite | Tests | Coverage |
| --- | --- | --- |
| Auth/RegisterTest | 4 | Registration validation, success, duplicate email |
| Auth/LoginTest | 4 | Login success, invalid credentials, validation |
| Auth/LogoutTest | 3 | Logout, unauthenticated access, current user |
| Project/CreateProjectTest | 4 | Create, validation, auto-generated code |
| Project/JoinProjectTest | 5 | Join by code, already member, invalid code |
| Project/ProjectCodeTest | 3 | Code uniqueness, format validation |
| Task/TaskTest | 9 | CRUD, status update, non-member rejection, filtering |
| Subtask/SubtaskTest | 8 | CRUD, status (assignee vs creator vs non-member) |
| Dashboard/DashboardTest | 3 | Stats, analytics, unauthenticated |
| Profile/ProfileTest | 4 | Get, update, email uniqueness, avatar upload |
| HealthTest | 1 | Health endpoint returns 200 |

Tests run against an **in-memory SQLite** database (configured in `phpunit.xml`) for speed and isolation.

---

## API Documentation

### OpenAPI / Swagger

The full **OpenAPI 3.1** specification lives at [`openapi.yaml`](openapi.yaml) in the project root.

**View interactively:**

```bash
# Swagger UI via Docker
docker run -p 8080:8080 \
  -e SWAGGER_JSON=/spec/openapi.yaml \
  -v $(pwd):/spec \
  swaggerapi/swagger-ui
# Open http://localhost:8080

# Or paste into https://editor.swagger.io
```

**Lint / validate:**

```bash
npx @redocly/cli lint openapi.yaml
```

### Postman Collection

| File | Purpose |
| --- | --- |
| [`postman_collection.json`](postman_collection.json) | All 26 requests in 7 folders |
| [`postman_environment.json`](postman_environment.json) | Environment variables |

**Import steps:**

1. Open Postman → **Import** → select `postman_collection.json`
2. **Environments → Import** → select `postman_environment.json`
3. Select **TaskFlow — Local** environment
4. Run **Login** first — `auth_token` is auto-set via test script
5. All subsequent requests inherit the Bearer token

**Environment variables:**

| Variable | Default | Description |
| --- | --- | --- |
| `base_url` | `http://localhost:8000/api` | API base URL |
| `auth_token` | _(auto-set)_ | Bearer token from login/register |
| `project_id` | `1` | Auto-set on project creation |
| `task_id` | `1` | Auto-set on task creation |
| `subtask_id` | `1` | Auto-set on subtask creation |

### Endpoint Summary (26 total)

| Method | Endpoint | Auth | Description |
| --- | --- | --- | --- |
| `GET` | `/api/health` | No | Health check |
| `POST` | `/api/v1/auth/register` | No | Register |
| `POST` | `/api/v1/auth/login` | No | Login |
| `POST` | `/api/v1/auth/logout` | Yes | Logout (revoke token) |
| `GET` | `/api/v1/auth/me` | Yes | Current user |
| `GET` | `/api/v1/projects` | Yes | List my projects |
| `POST` | `/api/v1/projects` | Yes | Create project |
| `GET` | `/api/v1/projects/{id}` | Yes | Get project with members/tasks |
| `POST` | `/api/v1/projects/join` | Yes | Join by code |
| `PATCH` | `/api/v1/projects/{id}/archive` | Yes | Archive (creator only) |
| `GET` | `/api/v1/projects/{id}/tasks` | Yes | List tasks (with filters) |
| `POST` | `/api/v1/projects/{id}/tasks` | Yes | Create task |
| `GET` | `/api/v1/tasks/{id}` | Yes | Get task with subtasks |
| `PUT` | `/api/v1/tasks/{id}` | Yes | Update task |
| `DELETE` | `/api/v1/tasks/{id}` | Yes | Delete task |
| `PATCH` | `/api/v1/tasks/{id}/status` | Yes | Update task status |
| `GET` | `/api/v1/tasks/{id}/subtasks` | Yes | List subtasks |
| `POST` | `/api/v1/tasks/{id}/subtasks` | Yes | Create subtask |
| `PUT` | `/api/v1/subtasks/{id}` | Yes | Update subtask |
| `DELETE` | `/api/v1/subtasks/{id}` | Yes | Delete subtask |
| `PATCH` | `/api/v1/subtasks/{id}/status` | Yes | Update subtask status |
| `GET` | `/api/v1/dashboard` | Yes | Dashboard stats |
| `GET` | `/api/v1/dashboard/analytics` | Yes | 30-day completion chart |
| `GET` | `/api/v1/profile` | Yes | Get profile |
| `PATCH` | `/api/v1/profile` | Yes | Update profile |
| `POST` | `/api/v1/profile/avatar` | Yes | Upload avatar (multipart) |

---

## Database Design

6 tables in PostgreSQL:

```
┌────────────┐       ┌──────────────────┐       ┌────────────┐
│   users    │──────<│ project_members   │>──────│  projects  │
│            │       │ (pivot)           │       │            │
│ id         │       │ project_id  FK    │       │ id         │
│ name       │       │ user_id     FK    │       │ code  UQ   │
│ email  UQ  │       │ joined_at         │       │ name       │
│ password   │       │ UNIQUE(proj,user) │       │ status     │
│ avatar     │       └──────────────────┘       │ created_by │
│ timestamps │                                   │ timestamps │
└────────────┘                                   └─────┬──────┘
      │                                                │
      │  assigned_to                                   │  project_id
      ▼                                                ▼
┌────────────┐                                  ┌────────────┐
│   tasks    │                                  │   tasks    │
│            │◄─────────────────────────────────│            │
│ id         │                                  │ id         │
│ project_id │                                  │ title      │
│ title      │       ┌────────────┐             │ status     │
│ status     │──────>│  subtasks  │             │ deadline   │
│ deadline   │       │            │             │ created_by │
│ created_by │       │ id         │             │ assigned_to│
│ assigned_to│       │ task_id FK │             │ timestamps │
│ timestamps │       │ body       │             └────────────┘
└────────────┘       │ status     │
                     │ deadline   │
                     │ assigned_to│
                     │ timestamps │
                     └────────────┘

+ personal_access_tokens (Laravel Sanctum)
```

**Status enum values:** `pending`, `working`, `done`

**Project status values:** `active`, `archived`, `done`

---

## Authentication & Authorisation

### Authentication

- **Laravel Sanctum** issues personal access tokens on register/login.
- Tokens are sent as `Authorization: Bearer <token>` on every protected request.
- Logout revokes the current token.

### Authorisation (Policies)

| Policy | Rule |
| --- | --- |
| **ProjectPolicy** | `view`, `createTask` → user must be a project member |
| **ProjectPolicy** | `manage` (archive) → user must be the project creator |
| **TaskPolicy** | `view`, `update`, `delete` → user must be a member of the task's project |
| **SubtaskPolicy** | `view`, `update`, `delete` → user must be a member of the subtask's task's project |
| **SubtaskPolicy** | `updateStatus` → user must be the subtask assignee **or** the project creator |

---

## Design Documents

| Document | Description |
| --- | --- |
| [`api_design.md`](api_design.md) | REST API contract — all endpoints, request/response schemas |
| [`DB_Design.md`](DB_Design.md) | Database schema — tables, columns, relationships, constraints |
| [`system_architecture.md`](system_architecture.md) | Architecture overview — modules, data flow, auth flow |
| [`MVP_scope_doc.md`](MVP_scope_doc.md) | MVP scope — functional requirements, feature list |
| [`plan.md`](plan.md) | Implementation plan — phased development roadmap |
