# Reference: Models & Factories

## Model Rules

- Models are `final class {Name} extends Model`
- Every model uses: `HasFactory`, `HasUuids`, `SoftDeletes`
- Explicit `$fillable` array — NEVER `$guarded = []`
- All fields documented with `@property` PHPDoc
- Relationship methods use typed return types (BelongsTo, HasMany, etc.)
- All date fields cast to `CarbonImmutable`

## PHPDoc Block

```php
/**
 * @property string $id
 * @property string $name
 * @property float $price
 * @property bool $is_active
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read User $user
 */
```

## Factory Rules

- Factory: `final class {Name}Factory extends Factory`
- `definition()` uses `fake()` helpers — no hardcoded values
- Create states for common variations (e.g., `inactive()`, `forUser()`)

## DO

```php
declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property float $price
 * @property bool $is_active
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read User $user
 */
final class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['user_id', 'name', 'price', 'is_active'];

    protected $casts = [
        'price'      => 'float',
        'is_active'  => 'boolean',
        'created_at' => CarbonImmutable::class,
        'updated_at' => CarbonImmutable::class,
        'deleted_at' => CarbonImmutable::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

## DON'T

```php
// ❌ No traits, guarded=[], no PHPDoc, no casts, no final
class Product extends Model
{
    protected $guarded = [];
}
```
