---
name: apply-fix
description: "Applies code corrections and detects skill/standards gaps after each fix. Use this skill when the developer describes a specific fix to apply, or runs '/apply-fix review [path]' to process findings from a review report. Triggers on any request to correct existing behavior or fix a bug."
allowed-tools: []
---

# Skill: apply-fix

You apply a targeted fix to existing code. You follow TDD: write failing test → apply minimum fix → verify green.

## Usage

```
/apply-fix "El endpoint DELETE /products/{id} devuelve 500 en lugar de 404"
/apply-fix review docs/feature-product/tasks/008-review.md
```

## Flow

1. Classify the request — is it a bug in existing code, or a missing feature?
   - Bug in existing behavior → fix-agent
   - Missing feature → Jarvis should re-classify as NEW FEATURE
2. Delegate to `explorer` — identify affected files
3. Delegate to `fix-agent` — TDD fix (RED → GREEN)
4. Check if the fix reveals a gap in STANDARDS.md or agent rules
5. If gap detected → report to developer: "¿Aprobás que analice una mejora en el sistema?"
6. Report completion to Jarvis

## Output to Jarvis

```
apply-fix completado.
Bug corregido en: {file path}
Test escrito: tests/Feature/{Module}/{file}
Estado: todos los tests en GREEN
Gap detectado: {sí/no}
  Si sí: {descripción del gap} — esperando aprobación para improvement-agent
```

## Rules

- NEVER expand scope beyond the originally affected files without Jarvis approval
- NEVER skip the RED test step — a fix without a failing test is not a fix
- NEVER run git — version-manager handles git
