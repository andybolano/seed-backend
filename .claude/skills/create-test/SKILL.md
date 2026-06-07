---
name: create-test
description: "Creates PHPUnit feature tests for a Seed Backend endpoint following the project's testing conventions. Use this skill when the developer runs '/create-test Module [--endpoint=POST /v1/resource]'. Generates a complete test file covering success, validation, authentication, not-found, and database state cases. Always the RED step — runs BEFORE /create-api."
allowed-tools: []
---

# Skill: create-test

You write failing PHPUnit feature tests for a specific endpoint BEFORE the implementation exists. This is the RED step in TDD.

## Usage

```
/create-test Product --endpoint=POST /api/v1/products
/create-test Product --endpoint=GET /api/v1/products
/create-test Product --endpoint=DELETE /api/v1/products/{id}
```

## Flow

1. Read the task specification file and SPEC
2. Read `docs/TESTING.md` to absorb all rules
3. Delegate to `executor-tests` — write the complete test file
4. Confirm the tests are in RED state (routes don't exist yet → 404 / route not found)
5. Report completion to Jarvis

## Mandatory reads before delegating

- The task `.md` file from `docs/feature-{slug}/tasks/`
- The SPEC: `docs/feature-{slug}/spec.md`
- `docs/TESTING.md`
- An existing test file for pattern consistency (e.g., `tests/Feature/`)

## Test cases to generate (per endpoint)

| Scenario | HTTP Method | Expected |
|----------|------------|---------|
| Happy path | Any | 200/201/204 + correct shape |
| Unauthenticated | Any | 401 |
| Validation error | POST/PUT/PATCH | 422 + field errors |
| Not found | GET/PUT/DELETE by ID | 404 |
| Forbidden (wrong role) | Any role-restricted | 403 |
| Soft delete check | DELETE | 204 + assertSoftDeleted |

## Output to Jarvis

```
create-test completado.
Archivo creado: tests/Feature/{Module}/{Module}Test.php
Tests en estado RED — rutas no existen aún.
Ejecutar: php artisan test tests/Feature/{Module}/{Module}Test.php
Siguiente paso: /create-api {Module} {Action}
```

## Rules

- NEVER write application code — tests only
- NEVER run git — version-manager handles all git operations
- All tests MUST use `route()` helper — never hardcode URIs
- All tests MUST use factories — never raw arrays
- Tests MUST be confirmed in RED state before reporting completion
