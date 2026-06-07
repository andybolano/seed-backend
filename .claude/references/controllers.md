# Reference: Controllers & FormRequests

## Controller Rules

- Controllers are `final class {Module}Controller extends ApiController`
- Constructor injects ONLY the corresponding Service
- Each public method handles ONE endpoint — named by HTTP verb convention (index, show, store, update, destroy) or custom action
- Zero business logic in controllers — ALL logic lives in the Service
- Return `JsonResponse` via ApiResponse trait helpers — NEVER raw `response()->json([])`
- NEVER return `['success' => true]` — trait helpers are the source of truth
- L5-Swagger `@OA` annotations required on every public method

## ApiResponse Trait Helpers

```php
$this->success($data, $message = null)     // 200
$this->created($data, $message = null)     // 201
$this->error($message, $code = 400)        // 4xx
$this->notFound($message)                  // 404
$this->unauthorized($message)              // 401
$this->forbidden($message)                 // 403
```

## FormRequest Rules

- FormRequests extend `Illuminate\Foundation\Http\FormRequest`
- `authorize()` returns `true` by default; add role check with `$this->user()->hasRole('admin')` when needed
- `rules()` returns an array with explicit validation rules
- `messages()` returns Spanish error messages for ALL rules (optional but recommended for user-facing errors)
- Never use `$request->all()` in controllers — use `$request->validated()`

## Route Registration

- Prefix: `api/v1/{resource}` (already in RouteServiceProvider)
- Named: `v1.{module}.{action}` or `v1.{module}s.{action}` (kebab-case)
- Use `Route::apiResource()` for standard CRUD
- Apply middleware groups: `['auth:sanctum', 'abilities:access', 'verified']` for protected routes

## DO

```php
// ✅ Correct Controller
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

final class ProductController extends ApiController
{
    public function __construct(private readonly ProductService $service) {}

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->validated());

        return $this->created(new ProductResource($product), 'Producto creado exitosamente.');
    }
}
```

## DON'T

```php
// ❌ Business logic in controller
public function store(Request $request): array
{
    $product = Product::create($request->all()); // logic here
    return ['success' => true];                  // wrong response format
}
```
