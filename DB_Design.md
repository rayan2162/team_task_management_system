# PHASE 4 — Database Design

## 1. Final Entities

Core tables:

1. users

2. projects

3. project_members

4. tasks

5. subtasks

6. personal_access_tokens (Sanctum)

# 2. ERD (Entity Relationship Diagram)

```text id="tjl3qf"
USERS
 ├── creates many PROJECTS
 ├── belongs to many PROJECTS (via project_members)
 ├── assigned many TASKS
 └── assigned many SUBTASKS


PROJECTS
 ├── belongs to creator (user)
 ├── has many members
 └── has many tasks


TASKS
 ├── belongs to project
 ├── belongs to assignee (user)
 └── has many subtasks


SUBTASKS
 ├── belongs to task
 └── belongs to assignee (user)
```

# 3. Table Design

## users

```sql id="n4yd6k"
id BIGINT PK

name VARCHAR(100)

email VARCHAR(255) UNIQUE

password VARCHAR(255)

avatar VARCHAR(500) NULL

created_at

updated_at
```

---

## projects

```sql id="fqjlwm"
id BIGINT PK

code VARCHAR(20) UNIQUE

name VARCHAR(150)

status ENUM
(active, archived, done)

created_by BIGINT FK users.id

created_at

updated_at
```

Notes:

Project code:

Example:

```text id="9o0gwy"
PRJX29K41
```

Must be unique.

Very important index.

---

## project_members (Pivot Table)

This is required for Join Project.

```sql id="8hjlwm"
id BIGINT PK

project_id BIGINT FK

user_id BIGINT FK

joined_at TIMESTAMP
```

Constraint:

One user cannot join same project twice.

Use unique composite:

```sql id="m2ptkl"
UNIQUE(project_id, user_id)
```

Very important.

---

## tasks

```sql id="pbvjlwm"
id BIGINT PK

project_id BIGINT FK

title VARCHAR(150)

status ENUM
(pending, working, done)

deadline DATE

created_by BIGINT FK

assigned_to BIGINT FK

created_at

updated_at
```

---

## subtasks

```sql id="7axj6p"
id BIGINT PK

task_id BIGINT FK

body TEXT

status ENUM
(pending, working, done)

deadline DATE

assigned_to BIGINT FK

created_at

updated_at
```

Excellent.

---

# 4. Foreign Key Constraints

## Projects

```sql id="2zpk8a"
created_by

REFERENCES users(id)
```

---

## Project Members

```sql id="mjlwm0"
project_id → projects.id

user_id → users.id
```

---

## Tasks

```sql id="i67f3d"
project_id → projects.id

created_by → users.id

assigned_to → users.id
```

---

## Subtasks

```sql id="r4mnkq"
task_id → tasks.id

assigned_to → users.id
```

---

# 5. Delete Rules (Important)

Use cascading carefully.

## If Project deleted:

Delete:

* memberships

* tasks

* subtasks

Use:

```sql id="r5s4zq"
ON DELETE CASCADE
```

Good.

---

## If User deleted

Do NOT cascade-delete projects/tasks.

Use:

```sql id="75nqj8"
ON DELETE SET NULL
```

for:

assigned_to

Safer.

Very important design decision.

---

# 6. Index Strategy

## Must Add Indexes

## users

```sql id="srsdzu"
INDEX(email)
```

(Though UNIQUE already indexes it.)

---

## projects

```sql id="nll1ix"
INDEX(code)

INDEX(created_by)
```

Project join lookup:

```http id="x3oqd7"
POST /joinProject/{code}
```

Needs fast lookup.

Critical index.

---

## project_members

```sql id="7ogcds"
INDEX(project_id)

INDEX(user_id)

UNIQUE(project_id, user_id)
```

Critical.

---

## tasks

```sql id="x0mj0n"
INDEX(project_id)

INDEX(assigned_to)

INDEX(status)

INDEX(deadline)
```

For dashboard queries.

Very important.

---

## subtasks

```sql id="yxjlwm"
INDEX(task_id)

INDEX(assigned_to)

INDEX(status)
```

Good.

---

# 7. Status Constraints

Do not allow random strings.

Use enum:

Tasks:

```text id="ejl20s"
pending

working

done
```

Subtasks:

Same.

Constraint prevents bad data.

Good architecture.

---

# 8. Laravel Migration Order

This matters.

Create in this order:

```text id="n9q6dc"
1 create_users_table

2 create_projects_table

3 create_project_members_table

4 create_tasks_table

5 create_subtasks_table
```

Must follow foreign key dependencies.

---

# 9. Laravel Relationships

## User Model

```php id="e6r1jv"
createdProjects()

projects()

assignedTasks()

assignedSubtasks()
```

---

## Project Model

```php id="wdjgo6"
creator()

members()

tasks()
```

---

## Task Model

```php id="93vwyi"
project()

assignee()

subtasks()
```

---

## Subtask Model

```php id="qenjlwm"
task()

assignee()
```

---

# 10. Query Examples This Design Supports

## Dashboard

Show my assigned tasks:

```sql id="b1rjlwm"
WHERE assigned_to = user_id
```

Fast because indexed.

---

## Join project by code

```sql id="jlwm1x"
WHERE code = ?
```

Fast because indexed.

---

## Project task listing

```sql id="9mvjlwm"
WHERE project_id = ?
```

Fast because indexed.

Good design.

---

# 11. Potential N+1 Risk

When loading project page:

Bad:

Project

→ tasks

→ subtasks

can cause N+1.

Use Eager Loading:

```php id="jlwm8u"
Project::with('tasks.subtasks')
```

Mention this in docs.

---

# 12. Final Schema Summary

```text id="jlwm7m"
users
  id
  name
  email
  password
  avatar

projects
  id
  code
  name
  status
  created_by

project_members
  id
  project_id
  user_id

tasks
  id
  project_id
  title
  status
  deadline
  assigned_to

subtasks
  id
  task_id
  body
  status
  deadline
  assigned_to
```

That is your MVP schema.

---