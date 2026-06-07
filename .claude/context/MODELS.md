# Backend Models Registry

> Registro acumulativo de todos los modelos Eloquent implementados.
> Actualizado automáticamente por `context-sync` después de cada feature.
> Los agentes leen este archivo al inicio de cada tarea para saber qué ya existe.

Last updated: 2026-06-07

---

## User

- **Tabla**: `users`
- **Primary key**: `id` (bigint, auto-increment — modelo base de Laravel)
- **Traits**: `HasFactory`, `Notifiable`, `HasApiTokens`, `HasRoles`
- **Implements**: `MustVerifyEmail`
- **Fillable**: `name`, `email`, `password`
- **Hidden**: `password`, `remember_token`
- **Casts**: `email_verified_at` → datetime, `password` → hashed

**Campos:**
| Campo | Tipo SQL | Nullable | Descripción |
|-------|----------|----------|-------------|
| id | bigint unsigned | no | PK auto-increment |
| name | varchar | no | Nombre completo |
| email | varchar | no | Email único |
| email_verified_at | timestamp | sí | Fecha de verificación |
| password | varchar | no | Hash bcrypt |
| remember_token | varchar(100) | sí | Token remember me |
| created_at | timestamp | no | — |
| updated_at | timestamp | no | — |

**Relaciones:**
- (ninguna implementada aún — listo para extender)

**Notas:**
- Usa `HasRoles` de spatie/laravel-permission para control de acceso basado en roles
- Tokens Sanctum separados por ability: `access` y `refresh`
- No usa `HasUuids` — es el modelo base de Laravel (los nuevos modelos de features SÍ deben usar UUID)

---

## Convenciones para modelos nuevos

Todo modelo creado por los executors debe:
- Usar `HasUuids`, `SoftDeletes`, `HasFactory`
- Primary key: `$table->uuid('id')->primary()`
- Siempre `softDeletes()` y `timestamps()`
- `$fillable` explícito (nunca `$guarded = []`)
- PHPDoc `@property` para todos los campos
- `final class`
