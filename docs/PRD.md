# PRD.md — Seed Backend Product Requirements

> This document describes the product, its current state, and its modules. Agents read this to understand what already exists before proposing changes.

---

## Product Overview

**Seed Backend** is a reusable Laravel 12 API starter kit. It provides all the standard authentication and user management infrastructure so new projects can start with auth already done and focus on their domain features.

**Stack:**
- Laravel 12 · PHP 8.3+
- PostgreSQL 16 (Docker, port 5432)
- Redis 7 (Docker, port 6379) — cache, queues, sessions
- Mailpit (Docker, SMTP 1025, UI 8025) — email in development
- Laravel Sanctum 4.x — token-based auth (access + refresh with abilities)
- Spatie Permissions 8.x — roles and permissions
- L5-Swagger 11.x — OpenAPI docs at `/api/documentation`

**Start the project:**
```bash
docker compose up -d
php artisan migrate:fresh --seed
php artisan serve --port=8080
```

---

## Existing Modules

### Auth Module — `app/Http/Controllers/Auth/`

**Endpoints:**

| Method | Path | Description | Auth |
|--------|------|-------------|------|
| POST | /api/v1/auth/register | Register + send email verification | Public |
| POST | /api/v1/auth/login | Returns access_token (1h) + refresh_token (30d) | Public |
| POST | /api/v1/auth/refresh | Rotate tokens — refresh token only | refresh ability |
| POST | /api/v1/auth/logout | Revoke all tokens | access ability |
| GET | /api/v1/auth/me | Authenticated user | access ability |
| GET | /api/v1/auth/email/verify/{id}/{hash} | Verify email (signed URL) | Signed |
| POST | /api/v1/auth/email/resend | Resend verification email | access ability |
| POST | /api/v1/auth/password/forgot | Request password reset (anti-enumeration) | Public |
| POST | /api/v1/auth/password/reset | Reset password | Public |

**Key files:**
- `app/Http/Controllers/Auth/AuthController.php`
- `app/Http/Controllers/Auth/ProfileController.php`
- `app/Http/Controllers/Auth/EmailVerificationController.php`
- `app/Http/Controllers/Auth/PasswordResetController.php`
- `app/Services/AuthService.php`
- `app/Notifications/VerifyEmailNotification.php`
- `app/Notifications/ResetPasswordNotification.php`

> **IMPORTANT:** Do not modify Auth controllers or AuthService — they are the stable base.

---

### Profile Module — `app/Http/Controllers/Auth/ProfileController.php`

| Method | Path | Description | Auth |
|--------|------|-------------|------|
| GET | /api/v1/profile | View profile | access + verified |
| PUT | /api/v1/profile | Edit profile | access + verified |
| PUT | /api/v1/profile/password | Change password (revokes sessions) | access + verified |
| DELETE | /api/v1/profile | Delete account | access + verified |

---

### User Management — `app/Http/Controllers/Api/V1/UserController.php`

| Method | Path | Description | Auth |
|--------|------|-------------|------|
| GET | /api/v1/users | List all users | admin role |
| GET | /api/v1/users/{id} | Show user detail | admin role |

---

## Existing Models

### User — `app/Models/User.php`

| Field | Type | Description |
|-------|------|-------------|
| id | uuid | Primary key |
| name | string | Full name |
| email | string | Unique |
| email_verified_at | timestamp | Null until verified |
| password | string | Hashed |
| remember_token | string | — |
| created_at | timestamp | — |
| updated_at | timestamp | — |
| deleted_at | timestamp | Soft delete |

**Relationships:**
- hasMany `PersonalAccessToken` (Sanctum)
- belongsToMany `Role` (Spatie)

---

## Existing Infrastructure

### `app/Traits/ApiResponse.php`

Provides response helpers available in all controllers via `extends ApiController`:
- `success($data, $message = null)` → 200
- `created($data, $message = null)` → 201
- `error($message, $code = 400)` → 4xx
- `notFound($message)` → 404
- `unauthorized($message)` → 401
- `forbidden($message)` → 403

### `app/Http/Controllers/Api/ApiController.php`

Base controller that uses `ApiResponse` trait. All new controllers extend this.

### `app/Http/Middleware/EnsureEmailIsVerified.php`

API version of email verification middleware — returns JSON 403 instead of redirect.

---

## Roles & Permissions

Managed by Spatie Laravel Permission:

| Role | Permissions |
|------|-------------|
| admin | users.view, users.create, users.edit, users.delete |
| user | (none — base authenticated access) |

**Seeded test users:**
- `admin@seed.dev` / `Admin1234!` (admin role, email verified)
- `user@seed.dev` / `User1234!` (user role, email verified)

---

## Middleware Available

| Middleware | Usage |
|-----------|-------|
| `auth:sanctum` | Requires valid Sanctum token |
| `abilities:access` | Token must have `access` ability |
| `abilities:refresh` | Token must have `refresh` ability |
| `verified` | Email must be verified (JSON 403 if not) |
| `role:admin` | User must have admin role |
| `permission:users.view` | User must have specific permission |
| `throttle:10,1` | Rate limit: 10 req/min |
| `throttle:5,1` | Rate limit: 5 req/min |

---

## Adding a New Module

When adding a new feature (e.g., Products), follow this structure:

```
app/Http/Controllers/Api/V1/Product/ProductController.php
app/Http/Requests/Product/StoreProductRequest.php
app/Http/Requests/Product/UpdateProductRequest.php
app/Http/Resources/ProductResource.php
app/Models/Product.php
app/Services/ProductService.php
database/migrations/XXXX_create_products_table.php
database/factories/ProductFactory.php
tests/Feature/Product/ProductTest.php
```

Routes go in `routes/api.php` under the appropriate middleware group.
