# Backend Services Registry

> Registro acumulativo de todos los servicios implementados.
> Actualizado automáticamente por `context-sync` después de cada feature.
> Los agentes leen este archivo al inicio de cada tarea para saber qué ya existe.

Last updated: 2026-06-07

---

## AuthService

**Namespace**: `App\Services\AuthService`
**Archivo**: `app/Services/AuthService.php`
**Responsabilidad**: Emisión, rotación y revocación de tokens Sanctum.

| Método | Firma | Retorno | Descripción |
|--------|-------|---------|-------------|
| issueTokens | `issueTokens(User $user): array` | `{access_token, refresh_token, token_type, expires_in}` | Crea par access+refresh token |
| rotateTokens | `rotateTokens(User $user): array` | `{access_token, refresh_token, token_type, expires_in}` | Revoca refresh actual y emite nuevos tokens |
| revokeAll | `revokeAll(User $user): void` | void | Revoca todos los tokens del usuario |

**Constantes internas:**
- `ACCESS_ABILITY = 'access'`
- `REFRESH_ABILITY = 'refresh'`
- `ACCESS_TTL = 60` (minutos)
- `REFRESH_TTL = 43200` (30 días en minutos)

**Notas:**
- No es `final class` — modelo base de Laravel
- Los servicios nuevos de features SÍ deben ser `final class`

---

## Convenciones para servicios nuevos

Todo servicio creado por executor-services debe:
- `final class XyzService`
- Sin herencia — composición sobre herencia
- Constructor injection del modelo o repositorio
- Métodos máx 20 líneas (extraer helpers privados)
- No usar `else` — guard clauses y early returns
- Throw exceptions en error — nunca retornar `null` silencioso
- Todas las queries: `Model::query()->where(..., '=', ...)` — nunca magic methods
