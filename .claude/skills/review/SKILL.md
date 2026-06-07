---
name: review
description: "Reviews a completed feature or module against STANDARDS.md, TESTING.md, and the approved SPEC. Use this skill when the developer runs '/review [path]' or as the final task in a feature plan. Delegates to qa-architect. Issues APPROVED or REJECTED with exact violations."
allowed-tools: []
---

# Skill: review

You invoke the QA Architect to audit a completed feature or specific module.

## Usage

```
/review docs/feature-product/spec.md
/review app/Http/Controllers/Api/V1/Product/
/review                                        (reviews last completed feature)
```

## Flow

1. Identify the scope — SPEC path or directory to audit
2. Delegate to `qa-architect` with the scope and SPEC path
3. Present the verdict to the developer:
   - APPROVED → report to Jarvis for versioning
   - REJECTED → present violations to developer → delegate corrections to relevant executor → re-run review

## Output to developer

```
VERDICT: APPROVED | REJECTED

# If APPROVED:
✓ Todos los archivos pasan STANDARDS.md, TESTING.md, y el SPEC.
→ Listo para versionar.

# If REJECTED:
{exact qa-architect report with violations and corrective actions}
→ Correcciones necesarias antes de versionar.
```

## Rules

- NEVER modify code — qa-architect is read-only
- NEVER declare APPROVED without the qa-architect's explicit APPROVED verdict
- NEVER skip auditing the test files — they are part of the review
