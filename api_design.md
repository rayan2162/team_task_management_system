# PHASE 5 — API Design (REST Contract)

## Base URL

```http id="v2j4mq"
http://localhost:8000/api
```

Versioned (recommended):

```http id="vh6j93"
http://localhost:8000/api/v1
```

Use versioning.

---

# 1. API Standards

## Response Format (Standardize everything)

Success:

```json id="1k86rx"
{
  "success": true,
  "message": "Task created successfully",
  "data": {}
}
```

Error:

```json id="jlwmq2"
{
  "success": false,
  "message": "Validation failed",
  "errors": {}
}
```

Use this everywhere.

Very important.

---

## Auth Header

```http id="jlwmr4"
Authorization: Bearer TOKEN
```

Required for protected routes.

---

# 2. Authentication APIs

## Register

```http id="jlwmf6"
POST /auth/register
```

Request:

```json id="jlwm2s"
{
"name":"John Doe",
"email":"john@email.com",
"password":"secret123",
"password_confirmation":"secret123"
}
```

Response:

```json id="jlwm3u"
{
"success":true,
"data":{
"user":{},
"token":"..."
}
}
```

---

## Login

```http id="jlwm4w"
POST /auth/login
```

Request:

```json id="jlwm5y"
{
"email":"john@email.com",
"password":"secret123"
}
```

Response:

```json id="jlwm6z"
{
"token":"..."
}
```

---

## Logout

```http id="jlwm70"
POST /auth/logout
```

Protected.

Revokes token.

---

## Current User

```http id="jlwm81"
GET /auth/me
```

Returns current user.

Very useful for React.

---

# 3. Project APIs

## Get My Projects

```http id="jlwm92"
GET /projects
```

Returns projects user belongs to.

Response:

```json id="jlwma3"
[
{
"id":1,
"name":"Marketing Project",
"code":"PRJ-X29K41",
"status":"active",
"created_by":2
}
]
```

---

## Create Project

```http id="jlwmb4"
POST /projects
```

Request:

```json id="jlwmc5"
{
"name":"Website Redesign"
}
```

Backend generates:

* project code

* creator

---

## Get Single Project

```http id="jlwmd6"
GET /projects/{projectId}
```

Returns:

* project info

* members

* summary counts

Good for project header.

---

## Join Project by Code

```http id="jlwme7"
POST /projects/join
```

Request:

```json id="jlwmf8"
{
"code":"PRJ-X29K41"
}
```

Do NOT use:

```http id="jlwmg9"
POST /joinProject/{code}
```

Put data in request body.

Much cleaner API design.

---

## Archive Project

Optional but good:

```http id="jlwmha"
PATCH /projects/{id}/archive
```

---

# 4. Task APIs

## Get Tasks for Project

```http id="jlwmib"
GET /projects/{projectId}/tasks
```

Supports query params:

```http id="jlwmjc"
?status=working
?assignee=4
?priority=high
```

Very useful.

---

## Create Task

```http id="jlwmkd"
POST /projects/{projectId}/tasks
```

Request:

```json id="jlwmle"
{
"title":"Build API",
"status":"pending",
"priority":"high",
"deadline":"2026-04-30",
"assigned_to":5
}
```

---

## Get Single Task

```http id="jlwmmf"
GET /tasks/{taskId}
```

Returns:

* task

* subtasks

Very useful.

---

## Update Task

```http id="jlwmng"
PUT /tasks/{taskId}
```

---

## Delete Task

```http id="jlwmoh"
DELETE /tasks/{taskId}
```

Must have.

---

## Update Task Status

```http id="jlwmpi"
PATCH /tasks/{taskId}/status
```

Request:

```json id="jlwmqj"
{
"status":"working"
}
```

Very important.

---

# 5. Subtask APIs

## Create Subtask

```http id="jlwmrk"
POST /tasks/{taskId}/subtasks
```

Request:

```json id="jlwmsl"
{
"body":"Implement auth middleware",
"status":"pending",
"deadline":"2026-04-25",
"assigned_to":4
}
```

---

## Get Task Subtasks

```http id="jlwmtm"
GET /tasks/{taskId}/subtasks
```

---

## Update Subtask

```http id="jlwmun"
PUT /subtasks/{id}
```

---

## Update Subtask Status

```http id="jlwmvo"
PATCH /subtasks/{id}/status
```

Required for dropdown.

---

## Delete Subtask

```http id="jlwmwp"
DELETE /subtasks/{id}
```

Do not forget this.

---

# 6. Dashboard APIs

## Dashboard Data

```http id="jlwmxq"
GET /dashboard
```

Returns:

```json id="jlwmyr"
{
"assigned_tasks":8,
"assigned_subtasks":12,
"completed_tasks":4,
"completion_rate":67
}
```

---

## Dashboard Graph Data

```http id="jlwmzs"
GET /dashboard/analytics
```

Returns:

```json id="jlwn0t"
[
{
"date":"2026-04-20",
"completed":5
}
]
```

Supports graph.

Important.

---

# 7. Profile APIs

## Get Profile

```http id="jlwn1u"
GET /profile
```

---

## Update Profile

```http id="jlwn2v"
PATCH /profile
```

---

## Upload Avatar

```http id="jlwn3w"
POST /profile/avatar
```

Multipart upload.

Do NOT combine with profile patch.

Separate endpoint is cleaner.

---

# 8. Authorization Rules (Critical)

Enforce:

## Projects

Only project members can:

```http id="jlwn4x"
GET /projects/{id}
```

---

## Tasks

Only project members can:

Create tasks.

---

## Subtasks

Only assignee OR project creator can:

Update subtask status.

Use Policies.

Very important.

---

# 9. Validation Rules

## Create Project

```text id="jlwn5y"
name required
max 150 chars
```

---

## Join Project

```text id="jlwn6z"
code required
must exist
```

---

## Task

```text id="jlwn70"
title required

deadline date

assigned_to exists
```

---

## Subtask

```text id="jlwn81"
body required

assigned_to exists
```

---

# 10. HTTP Status Codes

Use proper codes.

```http id="jlwn92"
200 OK

201 Created

401 Unauthorized

403 Forbidden

404 Not Found

422 Validation Error

500 Server Error
```

This matters.

---

# 11. Final Route Map

## Auth

```http id="jlwna3"
POST /auth/register
POST /auth/login
POST /auth/logout
GET /auth/me
```

---

## Projects

```http id="jlwnb4"
GET /projects
POST /projects
GET /projects/{id}
POST /projects/join
PATCH /projects/{id}/archive
```

---

## Tasks

```http id="jlwnc5"
GET /projects/{id}/tasks
POST /projects/{id}/tasks

GET /tasks/{id}
PUT /tasks/{id}
DELETE /tasks/{id}

PATCH /tasks/{id}/status
```

---

## Subtasks

```http id="jlwnd6"
POST /tasks/{id}/subtasks
GET /tasks/{id}/subtasks

PUT /subtasks/{id}
DELETE /subtasks/{id}

PATCH /subtasks/{id}/status
```

---

## Dashboard

```http id="jlwne7"
GET /dashboard
GET /dashboard/analytics
```

---

## Profile

```http id="jlwnf8"
GET /profile
PATCH /profile
POST /profile/avatar
```

---

# 12. Swagger / OpenAPI Coverage

Document ALL endpoints in Swagger.

Include:

* Request schemas

* Response schemas

* Auth headers

* Error responses

* Example payloads

Everything.

---

# 13. Postman Collection Folders

Create folders:

```text id="jlwng9"
Auth

Projects

Tasks

Subtasks

Dashboard

Profile
```


---

#  Health Check Endpoint

```http id="jlwnha"
GET /health
```

Returns:

```json id="jlwnib"
{
"status":"ok"
}
```