# PHASE 3 — System Architecture

## 1. Architecture Style

### Frontend Monolith

Single React application.

Contains:

* Auth module
* Dashboard module
* Projects module
* Tasks module
* Profile module


### Backend Monolith

Single Laravel application.

Contains:

* Auth domain
* User domain
* Project domain
* Task domain
* Subtask domain
* Dashboard domain

---

# 2. High-Level Architecture

```text id="jlwm6q"
Browser
↓
React Frontend
↓
REST API
↓
Laravel Backend
↓
PostgreSQL
```

---

## 3. Authentication Architecture


Laravel Sanctum (token auth)

Flow:

```text id="w52n3v"
Login
↓
Laravel issues token
↓
React stores token
↓
Axios sends:
Authorization: Bearer TOKEN
↓
Protected APIs
```

---

## 4. Core Modules

### Module 1 — Auth

Responsibilities:

* Register

* Login

* Logout

* Token validation


### Module 2 — Users

Responsibilities:

* Profile

* Avatar upload

* Assigned work


### Module 3 — Projects

Responsibilities:

* Create project

* Join Project (Using Project Code)

* View projects

* Project status

* Project owner


### Module 4 — Tasks

Responsibilities:

* Create task

* Assign task

* Deadlines

* Status


### Module 5 — Subtasks

Responsibilities:

* Nested under task

* Body/description

* Assignee

* Created date

* Deadline

* Status dropdown

### Module 6 — Dashboard

Responsibilities:

Show:

* My assigned tasks

* My subtasks

* Completion metrics

* Productivity graph

---

# 5. Domain Model

Relationships:

```text id="o6zhcm"
User
 ├── has many Projects

Project
 ├── has many Tasks

Task
 ├── has many Subtasks

Subtask
 ├── belongs to User (assignee)
```

This is clean.

---

# 6. Database Design

## users

```sql id="oikbx6"
id
name
email
password
avatar
created_at
```

---

## projects

```sql id="c7vbo0"
id
code
name
status
created_by
created_at
```

---

## tasks

```sql id="v4vdti"
id
project_id
title
status
deadline
created_by
assigned_to
created_at
```

---

## subtasks

```sql id="jphgb7"
id
task_id
body
status
deadline
assigned_to
created_at
```

---

# 7. API Architecture

## Auth

```http id="sl4m53"
POST /api/register

POST /api/login

POST /api/logout
```

## Projects

```http id="onx7u0"
GET /api/projects

POST /api/projects

GET /api/projects/{id}

POST /api/joinProject/{code}
```

## Tasks

```http id="37t2ry"
GET /api/projects/{id}/tasks

POST /api/tasks

PUT /api/tasks/{id}
```

## Subtasks

```http id="dqq8wq"
POST /api/subtasks

PATCH /api/subtasks/{id}/status
```

## Dashboard

```http id="dzcfxq"
GET /api/dashboard
```

## Profile

```http id="wzpt3c"
PATCH /api/profile
```

---

# 8. Frontend Architecture

## React Structure

```bash id="9fr29t"
src/

modules/

auth/

dashboard/

projects/

tasks/

profile/

components/

api/

routes/
```

---

# 9. Backend Architecture (Laravel)

Use DDD-lite:

```bash id="u7h88v"
app/

Domain/

Project/

Task/

Subtask/

Services/

Repositories/

Http/Controllers/

Policies/
```


---

# 10. UI Flow

## User Flow

```text id="h0a1fw"
Login/Register

↓

Home Dashboard

↓

Projects Page (Create/Join/Go to Individual Project)

↓

Individual Project

↓

Task

↓

Subtasks
```


---

# 11. Permissions (Simple)

User can:

* View assigned tasks

* Update assigned subtasks

Project creator can:

* Manage project

* Manage tasks

Simple authorization.

Use Policies.

---




# 15. Final Architecture Diagram

```text id="l42mfh"
React SPA
 ├── Dashboard
 ├── Projects
 ├── Tasks
 └── Profile

       ↓ REST API

Laravel Monolith
 ├── Auth Module
 ├── Project Module
 ├── Task Module
 ├── Subtask Module
 └── Dashboard Module

       ↓

PostgreSQL
```

