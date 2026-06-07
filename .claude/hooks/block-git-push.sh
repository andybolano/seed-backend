#!/bin/bash
# .claude/hooks/block-git-push.sh
# Hook PreToolUse para version-manager — bloquea git push sin confirmación explícita
# Recibe JSON de Claude Code por stdin; sale con exit 2 si detecta git push

INPUT=$(cat)
COMMAND=$(echo "$INPUT" | jq -r '.tool_input.command // empty')

# Detectar cualquier variante de git push
if echo "$COMMAND" | grep -qE '^\s*git\s+push(\s|$)'; then
    echo "BLOQUEADO: git push requiere confirmación explícita del usuario." >&2
    echo "El agente version-manager debe solicitar confirmación antes de empujar al remoto." >&2
    exit 2
fi

exit 0
