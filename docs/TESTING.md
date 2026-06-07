# TESTING.md — Seed Backend Testing Standards

> This document defines the testing standard. QA Architect and Validator enforce every rule defined here.

---

## 1. Framework

**PHPUnit** via `php artisan test`. All tests are in `tests/Feature/{Module}/`.

---

## 2. File Structure

```
tests/
└── Feature/
    └── {Module}/
        └── {Module}Test.php
```

One test class per module. If a module grows large, split by resource.

---

## 3. Test Class Rules

```php
declare(strict_types=1);

namespace Tests\Feature\{Module};

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class {Module}Test extends TestCase
{
    use RefreshDatabase;
    // ...
}
```

- `declare(strict_types=1)` mandatory
- Class: `final class {Module}Test extends TestCase`
- `use RefreshDatabase` on the class — NEVER per method

---

## 4. Test Method Rules

```php
public function test_it_should_{behavior}(): void
```

- Name: `test_it_should_{behavior}` in snake_case
- Alternative: `#[Test]` attribute + `it_should_{behavior}(): void`
- ALL methods MUST declare `: void` return type

---

## 5. AAA Pattern (Mandatory)

Every test MUST follow Arrange → Act → Assert with comments:

```php
public function test_it_should_create_product_and_return_201(): void
{
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->postJson(route('v1.products.store'), [
            'name'  => 'Widget',
            'price' => 9.99,
        ]);

    // Assert
    $response->assertCreated();
    $response->assertJsonStructure(['data' => ['id', 'name', 'price']]);
    $this->assertDatabaseHas('products', ['name' => 'Widget']);
}
```

---

## 6. Assertions

**ALWAYS use semantic helpers — NEVER raw integers:**

```php
// ✅ Correct
$response->assertOk()               // 200
$response->assertCreated()          // 201
$response->assertNoContent()        // 204
$response->assertUnauthorized()     // 401
$response->assertForbidden()        // 403
$response->assertNotFound()         // 404
$response->assertUnprocessable()    // 422
$response->assertConflict()         // 409

// ❌ Wrong
$response->assertStatus(200)
$response->assertStatus(404)
```

---

## 7. Routes

**ALWAYS use `route()` helper — NEVER hardcode URIs:**

```php
// ✅ Correct
route('v1.products.index')
route('v1.products.show', $product->id)

// ❌ Wrong
'/api/v1/products'
'/api/v1/products/' . $product->id
```

---

## 8. Factories

**ALWAYS use factories — NEVER raw arrays or DB calls:**

```php
// ✅ Correct
$user = User::factory()->create();
$product = Product::factory()->for($user)->create();

// ❌ Wrong
User::create(['name' => 'Test', 'email' => 'test@test.com', ...]);
DB::table('products')->insert([...]);
```

---

## 9. Authentication

```php
// ✅ For access token endpoints
$this->actingAs($user)

// ✅ For ability-specific tokens (refresh, etc.)
$this->actingAs($user, 'sanctum')
```

---

## 10. Coverage Requirements per Endpoint

| Scenario | Expected Status | Required |
|----------|----------------|----------|
| Happy path | 200/201/204 | ✅ Always |
| Unauthenticated | 401 | ✅ Always |
| Validation error | 422 | ✅ Always (for POST/PUT/PATCH) |
| Not found | 404 | ✅ For show/update/delete |
| Forbidden (wrong role) | 403 | ✅ For role-restricted endpoints |
| Soft delete check | 204 + assertSoftDeleted | ✅ For DELETE |

---

## 11. Database Assertions

```php
// Verify record created
$this->assertDatabaseHas('products', ['name' => 'Widget', 'user_id' => $user->id]);

// Verify soft delete
$this->assertSoftDeleted('products', ['id' => $product->id]);

// Verify record truly deleted (hard delete)
$this->assertDatabaseMissing('products', ['id' => $product->id]);
```

---

## 12. Running Tests

```bash
# All tests
php artisan test

# Specific file
php artisan test tests/Feature/Product/ProductTest.php

# Filter by method
php artisan test --filter test_it_should_create_product

# With coverage
php artisan test --coverage
```
