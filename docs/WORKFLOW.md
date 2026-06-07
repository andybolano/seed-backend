# WORKFLOW.md — Seed Backend Development Workflow

> This document defines the end-to-end process for every development request. All agents read this before operating.

---

## 1. Request Classification

Jarvis classifies every request before acting:

| Type | Definition | Flow |
|------|-----------|------|
| **NEW FEATURE** | New behavior, endpoints, models, modules | Full flow (SPEC → Plan → Execute → QA → Version) |
| **FIX** | Corrects existing behavior, zero structural changes | Explorer → Fix Agent → QA (optional) → Version |
| **PRECISE TASK** | Single pinpoint change, zero ambiguity | Execute directly → QA |
| **VALIDATION** | Compliance audit on existing module | Explorer → Validator → Report |
| **REFACTOR** | Code quality improvement, no behavior change | Validator → Refactor Agent → Tests → Version |

---

## 2. New Feature Flow

```
1. SPEC CREATION
   Jarvis → spec-creator (Explorer sub-agent included)
   Output: docs/feature-{slug}/spec.md
   Gate: Developer approves the SPEC

2. EXPLORATION
   Jarvis → explorer
   Output: Explorer context report (inline)

3. PLANNING
   Jarvis → planner (with SPEC + Explorer report)
   Output: docs/feature-{slug}/tasks/*.md + README.md
   Gate: Developer approves the task plan

4. BRANCH CREATION
   Jarvis → version-manager
   Action: git checkout -b feature/{slug}

5. EXECUTION (per task, in order)
   Jarvis → executor-tests       (RED — failing tests first)
   Jarvis → executor-migrations   (if migration task)
   Jarvis → executor-models       (if model task)
   Jarvis → executor-resources    (if resource task)
   Jarvis → executor-services     (if service task)
   Jarvis → executor-controllers  (GREEN — implementation)

6. QA
   Jarvis → qa-architect
   Output: APPROVED or REJECTED with violations
   If REJECTED: corrections → re-run executor → re-run QA

7. VERSIONING
   Gate: Developer approves push
   Jarvis → version-manager (commit → push → PR)
```

---

## 3. Fix Flow

```
1. EXPLORATION
   Jarvis → explorer (identify affected files)

2. FIX
   Jarvis → fix-agent (TDD: RED test → minimum fix → GREEN)

3. VERSIONING
   Gate: Developer approves push
   Jarvis → version-manager (commit → push → PR)
```

---

## 4. Validation Flow

```
1. EXPLORATION
   Jarvis → explorer (scope the module)

2. AUDIT
   Jarvis → validator
   Output: VALIDATOR_REPORT with violations + severity

3. DECISION
   Jarvis presents report to developer
   Developer decides: refactor | accept | urgent fix
```

---

## 5. Delegation Format

Jarvis delegates to agents using this EXACT format — no prose:

```
TASK:             {what the agent must accomplish}
EXECUTOR:         {agent name}
DIRECTORY:        {working directory or file path}
SUCCESS_CRITERIA: {what done looks like}
RESTRICTIONS:     {what the agent must NOT do}
DEPENDS_ON:       {task NNN or "none"}
```

---

## 6. Feature Directory Convention

Every feature uses: `docs/feature-{slug}/`

```
docs/feature-{slug}/
├── spec.md          # Feature specification (created by spec-creator)
└── tasks/
    ├── README.md    # Task index (created by planner)
    ├── 001-{slug}.md
    ├── 002-{slug}.md
    └── ...
```

The `feature-` prefix is NON-NEGOTIABLE. Never use `docs/{slug}/`.

---

## 7. Task File Format

```markdown
# Tarea {NNN}: {Título}

**Especificación:** `docs/feature-{slug}/spec.md`
**Estado:** Pendiente | En progreso | Completada
**Complejidad:** simple | complex
**Depende de:** {NNN} | Ninguna

## Objetivo
## Archivos a Crear/Modificar
## Archivos de Contexto
## Requisitos
## Criterios de Aceptación
## Skill a Usar
```

---

## 8. Explorer Report Format

```
FILES_ANALYZED:     {list}
MODELS_FOUND:       {name, file, fields, relationships}
EXISTING_ROUTES:    {HTTP verb, path, controller method, file:line}
SERVICES_FOUND:     {class, public methods}
PATTERNS_OBSERVED:  {concrete patterns with file paths}
CONTEXT_FOR_PLAN:   {naming conflicts, reusable code, middleware applied}
```

---

## 9. QA Report Format

```
VERDICT: APPROVED | REJECTED

# If REJECTED:
FILE_REVIEWED:     {full path}
RULES_VIOLATED:    [{rule}: {evidence} at line {N}]
CORRECTIVE_ACTION: {exact instruction} → executor: {agent-name}
```

---

## 10. Validator Report Format

```
VALIDATOR_REPORT
MODULE:          {module}
FILES_ANALYZED:  {count}

VIOLATIONS:
  - FILE: {path}
    LINE: {N}
    RULE: {rule from STANDARDS.md or TESTING.md}
    EVIDENCE: {actual code}
    SEVERITY: critical | warning

SUMMARY:
  CRITICAL: {count}
  WARNINGS: {count}
  RECOMMENDATION: refactor | acceptable | urgent
```

---

## 11. Git Rules

- Branch naming: `feature/{slug}`, `fix/{slug}`, `chore/{slug}`
- Commit format (Conventional Commits):
  ```
  feat: add product management endpoints
  fix: correct null check in product deletion
  chore: add migration for products table
  test: add feature tests for product CRUD
  docs: update swagger annotations for products
  ```
- Push and PR creation ALWAYS require explicit developer approval
- NEVER operate on `main` directly
- NEVER use `--no-verify`

---

## 12. Agent Boundaries (Non-Negotiable)

| Agent | Writes To | Never Writes To |
|-------|-----------|-----------------|
| explorer | — | anything |
| spec-creator | docs/feature-{slug}/spec.md | app/, database/, tests/ |
| planner | docs/feature-{slug}/tasks/ | app/, database/, tests/ |
| executor-migrations | database/migrations/ | app/, tests/ |
| executor-models | app/Models/, database/factories/ | database/migrations/, tests/ |
| executor-resources | app/Http/Resources/ | app/Models/, tests/ |
| executor-services | app/Services/ | database/, tests/, Controllers/ |
| executor-controllers | app/Http/Controllers/Api/V1/{M}/, app/Http/Requests/{M}/, routes/api.php | database/, tests/ |
| executor-tests | tests/Feature/{Module}/ | app/, database/ |
| fix-agent | affected files + tests/Feature/ | unrelated modules |
| qa-architect | — | anything |
| validator | — | anything |
| version-manager | git only | app/, database/, docs/ |
| improvement-agent | .claude/skills/, .claude/agents/, .claude/references/ | app/, database/, tests/ |
| refactor-agent | approved scope only | unrelated modules |
