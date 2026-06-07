# Reference: Tests (PHPUnit)

## Test File Rules

- `declare(strict_types=1)` on every test file
- Test class: `final class {Module}Test extends TestCase`
- `use RefreshDatabase` declared on the test class — NEVER per method
- Each test method: `public function test_it_should_{behavior}(): void`
  - Alternative: `#[Test]` attribute + `public function it_should_{behavior}(): void`
- ALL test methods MUST declare `: void` return type
- AAA pattern with comments: `// Arrange`, `// Act`, `// Assert`
- `route()` helper always — NEVER hardcode URIs
- Model factories always — NEVER raw `DB::insert()` or arrays
- `$this->actingAs($user)` for authenticated endpoints
- Semantic assertion helpers — NEVER raw status integers (`assertStatus(200)`)

## Semantic Assertion Helpers

```php
$response->assertOk()               // 200
$response->assertCreated()          // 201
$response->assertNoContent()        // 204
$response->assertUnauthorized()     // 401
$response->assertForbidden()        // 403
$response->assertNotFound()         // 404
$response->assertUnprocessable()    // 422
$response->assertConflict()         // 409
```

## Coverage Requirements per Endpoint

Every endpoint MUST have tests for:
1. Happy path (correct input → expected response + DB state)
2. Unauthenticated (no token → 401)
3. Validation failure (missing/invalid input → 422 with field errors)
4. Not found (non-existent resource → 404)
5. Forbidden (wrong role → 403) — when the endpoint has role restrictions
6. Soft delete verification (`assertSoftDeleted`) — for DELETE endpoints

## DO

```php
declare(strict_types=1);

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_return_product_list_for_authenticated_user(): void
    {
        // Arrange
        $user = User::factory()->create();
        Product::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($user)
            ->getJson(route('v1.products.index'));

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_it_should_return_401_when_unauthenticated(): void
    {
        // Act
        $response = $this->getJson(route('v1.products.index'));

        // Assert
        $response->assertUnauthorized();
    }
}
```

## DON'T

```php
// ❌ Hardcoded URI, raw status integer, no void, no RefreshDatabase
class ProductTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/api/v1/products');
        $response->assertStatus(200);
    }
}
```
