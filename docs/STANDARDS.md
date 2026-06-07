# STANDARDS.md — Seed Backend Coding Standards

> This document is the single source of truth for all coding rules. QA Architect and Validator enforce every rule defined here.

---

## 1. PHP Baseline

Every PHP file starts with:

```php
<?php

declare(strict_types=1);
```

No exceptions.

---

## 2. Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── ApiController.php      # Base controller — provides ApiResponse helpers
│   │   │   └── V1/
│   │   │       └── {Module}/          # Module controllers
│   │   └── Auth/                      # Auth controllers (do not modify)
│   ├── Requests/
│   │   └── {Module}/                  # FormRequest classes
│   └── Resources/
│       └── {Module}Resource.php       # API Resources
├── Models/
│   └── {Module}.php                   # Eloquent models
├── Services/
│   └── {Module}Service.php            # Business logic
└── Exceptions/                        # Custom exceptions

database/
├── migrations/
└── factories/

routes/
└── api.php                            # All routes

tests/
└── Feature/
    └── {Module}/
        └── {Module}Test.php
```

---

## 3. Controllers

- `final class {Module}Controller extends ApiController`
- Constructor injects ONLY the corresponding Service
- Each public method handles ONE endpoint — no business logic
- Return `JsonResponse` using ApiResponse helpers only
- NEVER `response()->json([])` directly
- NEVER `['success' => true]`
- L5-Swagger `@OA` annotations on all public methods
- Use `$request->validated()` — NEVER `$request->all()`

---

## 4. FormRequests

- Extend `Illuminate\Foundation\Http\FormRequest`
- `authorize()` returns `true` or role check
- `rules()` must be explicit and complete
- `messages()` in Spanish for user-facing errors

---

## 5. Services

- `final class {Module}Service`
- Constructor injection for dependencies
- Methods MAX 20 lines — extract private helpers
- No `else` blocks — guard clauses and early returns
- Throw exceptions on failure — NEVER return `null` silently
- Return models or Collections — never JsonResponse

---

## 6. Eloquent Rules

- ALWAYS: `Model::query()->where('column', '=', $value)`
- NEVER: `Model::where('column', $value)` — shorthand forbidden
- NEVER: `DB::insert/update/delete` in application code
- Use `firstOrFail()` when resource must exist
- Use `$model->fresh()` after update to return refreshed state

---

## 7. Models

- `final class {Name} extends Model`
- Traits: `HasFactory`, `HasUuids`, `SoftDeletes` — all three, always
- Explicit `$fillable` — NEVER `$guarded = []`
- `@property` PHPDoc for every field
- `CarbonImmutable` for all date casts

---

## 8. API Resources

- `final class {Module}Resource extends JsonResource`
- `toArray(Request $request): array` — typed return
- camelCase for JSON keys
- Use `$this->whenLoaded()` for lazy relationships
- NEVER expose: `password`, internal IDs, `pivot` raw data

---

## 9. Migrations

- UUID primary key: `$table->uuid('id')->primary()`
- Always: `$table->softDeletes()`, `$table->timestamps()`
- Foreign keys with `constrained()->cascadeOnDelete()`
- Anonymous class syntax: `return new class extends Migration`

---

## 10. Naming Conventions

- Files: PascalCase (`ProductService.php`, `ProductController.php`)
- Variables: camelCase (`$productFound`, `$userExists`)
- Routes: kebab-case (`v1.products.store`, `v1.product-categories.index`)
- DB tables: snake_case, plural (`products`, `product_categories`)
- JSON keys: camelCase (`isActive`, `createdAt`)
- English: all code, class names, variables, comments
- Spanish: all user-facing messages and API error messages

---

## 11. Response Format

All responses use the `ApiResponse` trait helpers:

```php
// Success
$this->success($data, $message = null)     // 200
$this->created($data, $message = null)     // 201
$this->noContent()                         // 204

// Errors
$this->error($message, $code = 400)        // 4xx
$this->notFound($message)                  // 404
$this->unauthorized($message)              // 401
$this->forbidden($message)                 // 403
```

Resources are always passed to the response — controllers NEVER build arrays manually.

---

## 12. Documentation

Every public controller method MUST have L5-Swagger annotations:

```php
/**
 * @OA\Post(
 *     path="/api/v1/products",
 *     tags={"Products"},
 *     summary="Crear producto",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=201, description="Producto creado"),
 *     @OA\Response(response=422, description="Error de validación")
 * )
 */
```
