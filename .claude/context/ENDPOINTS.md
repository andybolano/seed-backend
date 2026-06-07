# Backend Endpoints Registry

> Registro acumulativo de todos los endpoints implementados.
> Actualizado automáticamente por `context-sync` después de cada feature.
> Los agentes leen este archivo al inicio de cada tarea para saber qué ya existe.

Last updated: 2026-06-07

---

## Auth (`/api/v1/auth`)

| Método | Path | Auth | Rate limit | Controller | Descripción |
|--------|------|------|------------|------------|-------------|
| POST | /api/v1/auth/register | no | 10/min | AuthController::register | Registro de usuario |
| POST | /api/v1/auth/login | no | 10/min | AuthController::login | Login, devuelve access + refresh token |
| GET | /api/v1/auth/email/verify/{id}/{hash} | no (signed) | — | EmailVerificationController::verify | Verificar email vía enlace firmado |
| POST | /api/v1/auth/password/forgot | no | 5/min | PasswordResetController::forgot | Solicitar reset de contraseña |
| POST | /api/v1/auth/password/reset | no | 5/min | PasswordResetController::reset | Resetear contraseña |
| GET | /api/v1/auth/me | access token | — | AuthController::me | Datos del usuario autenticado |
| POST | /api/v1/auth/logout | access token | — | AuthController::logout | Revocar todos los tokens |
| POST | /api/v1/auth/email/resend | access token | 3/min | EmailVerificationController::resend | Reenviar email de verificación |
| POST | /api/v1/auth/refresh | refresh token | — | AuthController::refresh | Rotar tokens (revoca refresh actual) |

## Profile (`/api/v1/profile`)

| Método | Path | Auth | Controller | Descripción |
|--------|------|------|------------|-------------|
| GET | /api/v1/profile | access + verified | ProfileController::show | Ver perfil |
| PUT | /api/v1/profile | access + verified | ProfileController::update | Actualizar perfil |
| PUT | /api/v1/profile/password | access + verified | ProfileController::changePassword | Cambiar contraseña |
| DELETE | /api/v1/profile | access + verified | ProfileController::destroy | Eliminar cuenta |

## Users (`/api/v1/users`) — Admin only

| Método | Path | Auth | Controller | Descripción |
|--------|------|------|------------|-------------|
| GET | /api/v1/users | access + verified + admin | UserController::index | Listar usuarios |
| GET | /api/v1/users/{id} | access + verified + admin | UserController::show | Ver usuario por ID |

---

## Notas de autenticación

- **access token**: `Authorization: Bearer {token}` con ability `access`
- **refresh token**: `Authorization: Bearer {token}` con ability `refresh`
- **verified**: requiere email verificado (`middleware verified`)
- **admin**: requiere rol `admin` (`middleware role:admin`)
- Tokens manejados por Laravel Sanctum
- Access token TTL: 60 minutos
- Refresh token TTL: 30 días
