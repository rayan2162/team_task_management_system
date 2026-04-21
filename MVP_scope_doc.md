# MVP Scope Document (Phase 0)

---
## 1. Functional Requirements

* CRUD Task

* Track task status:

  * Pending
  * In Progress
  * Completed

* Frontend ↔ Backend integration


---
## 2. MVP Scope


### Module 1 — Authentication

Simple login/register (Laravel Sanctum)


### Module 2 — Task Management

Task fields:

* Title

* Description

* Status

* Priority

* Due Date

* Assignee

* CRUD


### Module 3 — Task Status Workflow

Statuses:

* Pending

* In Progress

* Completed

Can update status.


### Module 4 — Dashboard

Show:

* Total Tasks

* In Progress Tasks

* Completed Tasks

* Overdue Tasks

### Module 5 — API

REST API with:

* CRUD endpoints

* Swagger

* Postman Collection

### Module 6 — Testing

Using:

* Pest

Test:

* Task creation

* Status update

* Deletion

* Validation

### Module 7 — Deployment

Deploy:

* Frontend

* Backend

* Database


## 3. Future Plan

* Comments

* Notifications

* Real-time websockets

* Teams/organizations

* AI features

* File attachments

* Microservices

* Kubernetes

* Advanced RBAC

---

## 4. Success Criteria

### Functional

* All CRUD works

* Status workflow works

* Tests pass

* Deployment works


### Technical

* Clean architecture

* Good API design

* Docker works

* Swagger works


### Product

* Clean UI

* Good UX

* One differentiated feature


---

## 5. Technical Scope

### Architecture

Frontend Monolith

Backend Monolith

Connected via REST API

### Stack

Backend:

* Laravel

* PostgreSQL

* Redis

Testing:

* Pest

Frontend:

* React + TypeScript

DevOps:

* Docker

Deployment:

* Vercel

* Railway

---

## 6. Constraints

Important constraints:

* Expected effort:
  3–4 hours

* Deadline:
  2–3 days

Therefore:

Optimize for:

* Clarity

* Reliability

* Delivery

---

## 7. Risks

| Risk                | Mitigation               |
| ------------------- | ------------------------ |
| Overbuilding        | Strict MVP scope         |
| Time overrun        | One wow feature only     |
| Frontend complexity | Use simple UI components |
| Deployment issues   | Docker first             |
| Testing skipped     | Write tests early        |

