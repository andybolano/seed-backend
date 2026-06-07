# General Standards — All PHP Files

These rules apply to EVERY PHP file in the project. No exceptions.

## Mandatory First Line

```php
<?php

declare(strict_types=1);
```

## Imports

- ALWAYS use `use` statements for ALL classes, interfaces, and enums
- NEVER use inline FQCN (e.g., `\App\Services\ProductService`)
- Includes PHP built-ins: `DateTimeInterface`, `Throwable`, `Stringable` — never prefix with `\`

## Typing

- ALL parameters, return types, and properties MUST be explicitly typed
- No `mixed` types unless absolutely necessary
- Use union types (`string|int`) and nullable types (`?string`) where appropriate

## Naming

- English only: variables, methods, class names, comments
- Spanish only: ALL user-facing messages (validation errors, exception messages, API responses)
- Descriptive names — avoid abbreviations and generic placeholder names
- Variables that represent existence or result state MUST include domain context:
  `$productFound`, `$userExists`, `$orderActive` — NEVER `$exists`, `$result`, `$data`, `$found`

## Control Flow

- No `else` blocks — use early returns and guard clauses
- No nested conditions deeper than 2 levels — extract to private methods
- Use `throw_if()` for single-throw guards — when an `if` block contains only a `throw`,
  replace with `throw_if(condition: ..., exception: ...)`. If the block has other logic, keep the `if`.

## Methods

- MAX 20 lines per method
- Each method does one thing
- Delegate to private methods when a step can be named
- Main orchestration method reads like a story

## PHP 8.3+ Features

- Constructor promotion for services, value objects
- `readonly` properties where applicable
- Backed `enum` for all enumerations (status, types, etc.)
- Named arguments in constructor calls where clarity improves readability
- `match` expressions instead of `switch`
- PHP attributes for metadata

## Domain Rules

- No magic strings for domain identifiers — centralize in backed enums
- DRY for database operations — extract shared queries to private Service methods

## Collections & Arrays

- `@var Type[]` or `array<key, value>` PHPDoc for typed arrays
- `Collection<int, Type>` PHPDoc for Eloquent collections
- Prefer Laravel Collection methods over raw loops

## Comments

- Comments only when the WHY is non-obvious
- English only for comments
- `/** @var Type */` inline annotations for type hints IDE cannot infer
- Do NOT over-comment obvious code
