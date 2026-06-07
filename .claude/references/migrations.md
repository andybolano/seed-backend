# Reference: Migrations

## Migration Rules

- Every table uses UUID primary key: `$table->uuid('id')->primary()`
- Every table has soft deletes: `$table->softDeletes()`
- Every table has timestamps: `$table->timestamps()`
- Foreign keys: `$table->foreignUuid('{relation}_id')->constrained()->cascadeOnDelete()`
  (unless spec specifies different behavior)
- Indexes declared AFTER all column definitions
- `down()` must correctly reverse `up()` — `Schema::dropIfExists()` for new tables
- `declare(strict_types=1)` mandatory
- Migration classes use anonymous class syntax: `return new class extends Migration`

## Column Types

| PHP Type | Migration Method |
|----------|-----------------|
| string | `->string('name')` or `->string('name', 100)` |
| text | `->text('description')` |
| int | `->integer('quantity')` |
| float/decimal | `->decimal('price', 10, 2)` |
| bool | `->boolean('is_active')->default(true)` |
| uuid FK | `->foreignUuid('user_id')->constrained()->cascadeOnDelete()` |
| enum | `->string('status')` with check constraint |
| json | `->json('metadata')->nullable()` |

## DO

```php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

## DON'T

```php
// ❌ Integer PK, no softDeletes, no strict types, old class syntax
class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
}
```
