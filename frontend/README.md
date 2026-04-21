# TaskFlow Frontend — React 19 SPA

The frontend for the TaskFlow team task management system. A single-page application built with **React 19**, **TypeScript**, **Vite 8**, **Tailwind CSS 4**, **Zustand** for state management, and **TanStack React Query** for server-state caching.

---

## Table of Contents

- [Architecture](#architecture)
- [Directory Structure](#directory-structure)
- [Setup](#setup)
- [Tech Stack](#tech-stack)
- [Routing](#routing)
- [State Management](#state-management)
- [API Layer](#api-layer)
- [React Query Hooks](#react-query-hooks)
- [Feature Modules](#feature-modules)
- [Shared Components](#shared-components)
- [TypeScript Types](#typescript-types)
- [Build & Production](#build--production)
- [Docker](#docker)

---

## Architecture

The frontend follows a **feature-based module** structure:

```
main.tsx
  → App.tsx
    → router.tsx (React Router v7)
        ├── /login         → LoginPage
        ├── /register      → RegisterPage
        └── AuthGuard (protected)
            └── AppLayout (Navbar + content area)
                ├── /dashboard     → DashboardPage
                ├── /projects      → ProjectsPage
                ├── /projects/:id  → ProjectDetailPage
                └── /profile       → ProfilePage
```

**Key principles:**
- Feature modules are self-contained — each module owns its pages, sub-components, and modals.
- Shared UI components live in `components/ui/` and `components/layout/`.
- All API calls go through a centralised Axios client with interceptors.
- Server state is managed by React Query hooks (automatic caching, refetching, mutation invalidation).
- Client state (auth token + user) is managed by a Zustand store persisted to localStorage.
- Type definitions for all API entities live in a single `types/index.ts`.

---

## Directory Structure

```
src/
├── api/                             ← Axios API modules
│   ├── client.ts                    ← Axios instance (base URL, token interceptor)
│   ├── auth.ts                      ← register, login, logout, getMe
│   ├── projects.ts                  ← getProjects, getProject, createProject, joinProject, archiveProject
│   ├── tasks.ts                     ← getTasks, getTask, createTask, updateTask, deleteTask, updateTaskStatus
│   ├── subtasks.ts                  ← getSubtasks, createSubtask, updateSubtask, deleteSubtask, updateSubtaskStatus
│   ├── dashboard.ts                 ← getDashboardStats, getDashboardAnalytics
│   └── profile.ts                   ← getProfile, updateProfile, uploadAvatar
│
├── components/
│   ├── guards/
│   │   └── AuthGuard.tsx            ← Redirects to /login if unauthenticated
│   ├── layout/
│   │   ├── AppLayout.tsx            ← Navbar + Outlet wrapper
│   │   └── Navbar.tsx               ← Top navigation with user menu
│   └── ui/
│       ├── Avatar.tsx               ← User avatar with initials fallback
│       ├── LoadingSpinner.tsx        ← Full-page loading indicator
│       ├── Modal.tsx                 ← Reusable modal dialog (backdrop click to close)
│       └── StatusBadge.tsx           ← Coloured status pill (pending/working/done)
│
├── hooks/                           ← React Query hooks
│   ├── useAuth.ts                   ← useLogin, useRegister, useLogout
│   ├── useProjects.ts               ← useProjects, useProject, useCreateProject, useJoinProject, useArchiveProject
│   ├── useTasks.ts                  ← useTasks, useCreateTask, useUpdateTask, useDeleteTask, useUpdateTaskStatus
│   ├── useSubtasks.ts               ← useCreateSubtask, useUpdateSubtask, useDeleteSubtask, useUpdateSubtaskStatus
│   ├── useDashboard.ts              ← useDashboardStats, useDashboardAnalytics
│   └── useProfile.ts                ← useProfile, useUpdateProfile, useUploadAvatar
│
├── modules/                         ← Feature modules (pages)
│   ├── auth/
│   │   ├── LoginPage.tsx            ← Email/password login form
│   │   └── RegisterPage.tsx         ← Registration form with password confirmation
│   ├── dashboard/
│   │   └── DashboardPage.tsx        ← Stats cards + 30-day completion chart (Recharts)
│   ├── projects/
│   │   ├── ProjectsPage.tsx         ← Grid of project cards + create/join modals
│   │   ├── ProjectDetailPage.tsx    ← Full project view: members, tasks, subtasks
│   │   └── components/
│   │       ├── ProjectCard.tsx      ← Project card with member count, task stats
│   │       ├── CreateProjectModal.tsx
│   │       └── JoinProjectModal.tsx
│   ├── tasks/
│   │   └── components/
│   │       ├── TaskSection.tsx      ← Expandable task list with filters
│   │       ├── SubtaskRow.tsx       ← Subtask row with inline status toggle
│   │       ├── CreateTaskModal.tsx
│   │       └── CreateSubtaskModal.tsx
│   └── profile/
│       └── ProfilePage.tsx          ← Edit name/email + avatar upload
│
├── stores/
│   └── authStore.ts                 ← Zustand store: token, user, setAuth, clearAuth
│
├── types/
│   └── index.ts                     ← All TypeScript interfaces
│
├── router.tsx                       ← React Router configuration
├── App.tsx                          ← QueryClientProvider + RouterProvider
├── main.tsx                         ← Entry point (renders App)
└── index.css                        ← Tailwind CSS imports
```

---

## Setup

### Prerequisites

- Node.js 20+
- npm 10+

### Installation

```bash
cd frontend
npm install
npm run dev
```

The development server starts at `http://localhost:5173`.

### API Proxy

Vite is configured to proxy `/api` requests to `http://localhost:8000` (the Laravel backend):

```js
// vite.config.js
server: {
  proxy: {
    '/api': 'http://localhost:8000',
    '/storage': 'http://localhost:8000',
  }
}
```

The `/storage` proxy enables avatar images to load during development.

---

## Tech Stack

| Library | Version | Purpose |
| --- | --- | --- |
| **React** | 19 | UI library |
| **TypeScript** | 5.x | Type safety |
| **Vite** | 8 | Build tool + HMR dev server |
| **Tailwind CSS** | 4 | Utility-first CSS |
| **React Router DOM** | 7.x | Client-side routing |
| **TanStack React Query** | 5.x | Server-state management (caching, mutations, invalidation) |
| **Zustand** | 5.x | Client-state management (auth token, user) |
| **Axios** | 1.x | HTTP client with interceptors |
| **Recharts** | 2.x | Charts for dashboard analytics |

---

## Routing

Defined in `router.tsx`:

| Path | Component | Auth Required |
| --- | --- | --- |
| `/login` | `LoginPage` | No |
| `/register` | `RegisterPage` | No |
| `/dashboard` | `DashboardPage` | Yes |
| `/projects` | `ProjectsPage` | Yes |
| `/projects/:id` | `ProjectDetailPage` | Yes |
| `/profile` | `ProfilePage` | Yes |
| `/` | Redirects to `/dashboard` | Yes |

All authenticated routes are wrapped in `AuthGuard`, which checks the Zustand auth store for a token and redirects to `/login` if absent.

---

## State Management

### Server State — React Query

All API data is managed through React Query hooks:

- **Automatic caching** — data is cached by query key (e.g. `['projects']`, `['project', id]`).
- **Background refetching** — stale data is refreshed automatically.
- **Mutation invalidation** — after a mutation (create, update, delete), related queries are invalidated to refetch fresh data.
- **Loading/error states** — every hook exposes `isLoading`, `error`, and `data`.

### Client State — Zustand

The `authStore` manages:

```typescript
interface AuthState {
  token: string | null;
  user: User | null;
  setAuth: (token: string, user: User) => void;
  clearAuth: () => void;
}
```

- Persisted to `localStorage` via Zustand's `persist` middleware.
- On app load, the token is read from localStorage and attached to Axios via interceptor.
- On logout, `clearAuth()` removes both the token and user from the store and localStorage.

---

## API Layer

### Axios Client (`api/client.ts`)

- Base URL: `/api` (proxied to backend in dev, same-origin in production).
- **Request interceptor:** attaches `Authorization: Bearer <token>` from the Zustand auth store.
- **Response interceptor:** on 401, clears auth state and redirects to `/login`.

### Module Pattern

Each API module exports functions that return typed Promises:

```typescript
// api/projects.ts
export const getProjects = () =>
  client.get<ApiResponse<Project[]>>('/v1/projects').then(r => r.data.data);

export const createProject = (data: CreateProjectData) =>
  client.post<ApiResponse<Project>>('/v1/projects', data).then(r => r.data.data);
```

---

## React Query Hooks

Each hook wraps the corresponding API module:

| Hook File | Exported Hooks |
| --- | --- |
| `useAuth.ts` | `useLogin()`, `useRegister()`, `useLogout()` |
| `useProjects.ts` | `useProjects()`, `useProject(id)`, `useCreateProject()`, `useJoinProject()`, `useArchiveProject()` |
| `useTasks.ts` | `useTasks(projectId, filters)`, `useCreateTask()`, `useUpdateTask()`, `useDeleteTask()`, `useUpdateTaskStatus()` |
| `useSubtasks.ts` | `useCreateSubtask()`, `useUpdateSubtask()`, `useDeleteSubtask()`, `useUpdateSubtaskStatus()` |
| `useDashboard.ts` | `useDashboardStats()`, `useDashboardAnalytics()` |
| `useProfile.ts` | `useProfile()`, `useUpdateProfile()`, `useUploadAvatar()` |

**Mutation hooks** automatically invalidate related queries on success. For example, `useCreateTask()` invalidates `['tasks', projectId]` and `['project', projectId]` to refresh both the task list and the project detail view.

---

## Feature Modules

### Auth (`modules/auth/`)

- **LoginPage** — email/password form, error display, link to register.
- **RegisterPage** — name/email/password/confirm form, auto-login on success.

### Dashboard (`modules/dashboard/`)

- **DashboardPage** — four stat cards (assigned tasks, completed tasks, assigned subtasks, completion rate) + an area chart showing daily completions over the last 30 days (Recharts `AreaChart`).

### Projects (`modules/projects/`)

- **ProjectsPage** — responsive grid of `ProjectCard` components with "Create Project" and "Join Project" buttons opening modals.
- **ProjectDetailPage** — shows project info, member list (`Avatar` components), and task sections. Includes "Create Task" button.
- **ProjectCard** — displays project name, code, status badge, member count, and task count.
- **CreateProjectModal** — form with project name input.
- **JoinProjectModal** — form with project code input.

### Tasks (`modules/tasks/`)

- **TaskSection** — renders all tasks for a project with filters (status, assigned_to). Each task expands to show subtasks.
- **SubtaskRow** — subtask displayed inline with status badge and clickable status toggle (cycles through pending → working → done).
- **CreateTaskModal** — form with title, status, deadline, and assignee (dropdown of project members).
- **CreateSubtaskModal** — form with body, status, deadline, and assignee.

### Profile (`modules/profile/`)

- **ProfilePage** — avatar upload (click-to-change with file input), editable name and email fields.

---

## Shared Components

| Component | Props | Description |
| --- | --- | --- |
| `AuthGuard` | `children` | Checks auth token; redirects to `/login` if missing |
| `AppLayout` | — | Renders `Navbar` + React Router `<Outlet />` |
| `Navbar` | — | Top bar with navigation links, user avatar, and logout |
| `Avatar` | `user`, `size?` | Displays avatar image or initials fallback (coloured circle) |
| `StatusBadge` | `status` | Coloured pill: blue (pending), amber (working), green (done) |
| `Modal` | `isOpen`, `onClose`, `title`, `children` | Overlay modal with title bar and backdrop click-to-close |
| `LoadingSpinner` | — | Centred spinning indicator |

---

## TypeScript Types

All types are in `types/index.ts`:

```typescript
interface User {
  id: number;
  name: string;
  email: string;
  avatar: string | null;
  created_at: string;
}

interface Project {
  id: number;
  code: string;
  name: string;
  status: 'active' | 'archived' | 'done';
  created_by: number;
  creator?: User;
  members?: User[];
  tasks?: Task[];
  created_at: string;
  updated_at: string;
}

interface Task {
  id: number;
  project_id: number;
  title: string;
  status: 'pending' | 'working' | 'done';
  deadline: string | null;
  created_by: number;
  assigned_to: number | null;
  creator?: User;
  assignee?: User;
  subtasks?: Subtask[];
  created_at: string;
  updated_at: string;
}

interface Subtask {
  id: number;
  task_id: number;
  body: string;
  status: 'pending' | 'working' | 'done';
  deadline: string | null;
  assigned_to: number | null;
  assignee?: User;
  created_at: string;
  updated_at: string;
}

interface DashboardStats {
  assigned_tasks: number;
  completed_tasks: number;
  assigned_subtasks: number;
  completed_subtasks: number;
  completion_rate: number;
}

interface DailyCompletion {
  date: string;
  tasks: number;
  subtasks: number;
}

interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}
```

---

## Build & Production

```bash
npm run build    # Outputs to dist/
npm run preview  # Preview production build locally
```

The production build is a static bundle served by any web server. The Docker setup uses Nginx.

---

## Docker

```dockerfile
# Multi-stage build
# Stage 1 — Node 20 Alpine: npm install + npm run build
# Stage 2 — Nginx Alpine: copy dist/ + custom nginx.conf
```

The `nginx.conf` handles SPA routing by falling back to `index.html` for all non-file paths, and proxies `/api` and `/storage` to the backend service.

| Config Detail | Value |
| --- | --- |
| Build output | `/usr/share/nginx/html` |
| Container port | `80` (mapped to `3000` in docker-compose) |
| SPA fallback | `try_files $uri $uri/ /index.html` |
| API proxy | `/api` → `http://nginx:8000` |
| Storage proxy | `/storage` → `http://nginx:8000` |
