---
name: create-api
description: "Creates a complete API endpoint for the Seed Backend: Service logic, Controller, FormRequests, Resource, and route registration. Use this skill when the developer runs '/create-api Module Action [--endpoint=POST /v1/resource]'. Always triggered after tests are written (RED step). Delegates to executor-services and executor-controllers in sequence."
allowed-tools: []
---

# Skill: create-api

You receive a task specification and implement the full HTTP layer + service logic for one endpoint.

## Usage

```
/create-api Product Store --endpoint=POST /api/v1/products
/create-api Product Index --endpoint=GET /api/v1/products
/create-api Product Show --endpoint=GET /api/v1/products/{id}
/create-api Product Update --endpoint=PUT /api/v1/products/{id}
/create-api Product Destroy --endpoint=DELETE /api/v1/products/{id}
```

## Flow

1. Read the task specification file provided by Jarvis
2. Read `docs/STANDARDS.md` to absorb all rules
3. Delegate to `executor-services` — implement business logic in `app/Services/{Module}Service.php`
4. Delegate to `executor-controllers` — implement controller, requests, and route registration
5. Report completion to Jarvis with files modified

## Mandatory reads before delegating

- The task `.md` file from `docs/feature-{slug}/tasks/`
- The SPEC: `docs/feature-{slug}/spec.md`
- `docs/STANDARDS.md`
- The existing Service (if updating) or `app/Services/AuthService.php` (if creating new)
- The existing Controller (if updating) or `app/Http/Controllers/Api/V1/UserController.php` (if creating new)

## Output to Jarvis

```
create-api completado.
Archivos creados/modificados:
  - app/Services/{Module}Service.php
  - app/Http/Controllers/Api/V1/{Module}/{Module}Controller.php
  - app/Http/Requests/{Module}/{Action}{Module}Request.php
  - routes/api.php (route added)
Tests a ejecutar: php artisan test tests/Feature/{Module}/{Module}Test.php
```

## Rules

- NEVER write tests — tests are written BEFORE this skill runs (RED step)
- NEVER run git — version-manager handles all git operations
- Service methods MAX 20 lines — extract private helpers if needed
- Controller methods have NO business logic — delegate everything to Service
- All routes use named convention: `v1.{module}s.{action}`
