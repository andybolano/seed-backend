# Reference: Services

## Service Rules

- Services are `final class {Module}Service`
- Constructor injection for all dependencies — never use `app()` or `resolve()`
- All public methods have explicit parameter types and return types
- Methods MAX 20 lines — extract to private named methods
- No `else` blocks — guard clauses and early returns
- Return Eloquent models, Collections, or primitives — never JsonResponse
- Throw domain exceptions on failure — never return `null` silently

## Eloquent Query Rules

- ALWAYS use `Model::query()->...` — NEVER `Model::where()->...`
- ALWAYS use explicit comparison: `->where('column', '=', $value)` — NEVER shorthand `->where('column', $value)`
- Use `firstOrFail()` when the entity must exist — never `first()` followed by a null check
- NEVER use `DB::insert/update/delete` in application code — always Eloquent

## Exception Handling

- Use `ModelNotFoundException` (thrown by `firstOrFail()`) — let it bubble up to the handler
- Throw `\Symfony\Component\HttpKernel\Exception\HttpException` subclasses for domain failures
- Or create custom exceptions in `app/Exceptions/`

## DO

```php
declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

final class ProductService
{
    public function getAll(): Collection
    {
        return Product::query()
            ->where('is_active', '=', true)
            ->orderBy('name')
            ->get();
    }

    public function findOrFail(string $id): Product
    {
        return Product::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    public function update(string $id, array $data): Product
    {
        $product = $this->findOrFail($id);
        $product->update($data);

        return $product->fresh();
    }

    public function delete(string $id): void
    {
        $product = $this->findOrFail($id);
        $product->delete();
    }
}
```

## DON'T

```php
// ❌ Shorthand where, else block, silent null return
public function find($id)
{
    $product = Product::where('id', $id)->first(); // shorthand where
    if ($product) {
        return $product;
    } else {
        return null; // silent failure — throw instead
    }
}
```
