# Context Sync — seed-backend

Escanea el codebase actual y actualiza los tres registros de contexto usados por los agentes ejecutores.
Ejecutar después de cada feature completada para que los próximos agentes sepan qué existe.

**Uso:** `/context-sync`

**Comportamiento:** Autónomo. Solo agregar o corregir. Nunca eliminar entradas existentes.
**Comandos Bash:** todos con prefijo `cd /Users/andyb/Documents/seed-app/seed-backend &&`

---

## Paso 1 — Sincronizar ENDPOINTS.md

Leer el archivo de rutas:
```bash
cd /Users/andyb/Documents/seed-app/seed-backend && cat routes/api.php
```

Para cada ruta registrada:
1. Identificar: método HTTP, path completo, middleware aplicado, Controller::method
2. Comparar con lo documentado en `.claude/context/ENDPOINTS.md`
3. Agregar rutas faltantes en el grupo correcto
4. Corregir entradas que hayan cambiado (middleware, controller, path)

**No eliminar** rutas existentes en el registro — solo agregar o corregir.

---

## Paso 2 — Sincronizar MODELS.md

Listar todos los modelos:
```bash
cd /Users/andyb/Documents/seed-app/seed-backend && find app/Models -name "*.php" | sort
```

Para cada modelo encontrado:
1. Leer el archivo completo
2. Extraer: tabla, primary key, traits, fillable, hidden, casts, campos de migración asociada
3. Extraer relaciones: `hasMany`, `belongsTo`, `hasOne`, `belongsToMany`
4. Comparar con `.claude/context/MODELS.md`
5. Agregar modelos faltantes o corregir campos que hayan cambiado

Para encontrar la migración asociada:
```bash
cd /Users/andyb/Documents/seed-app/seed-backend && find database/migrations -name "*{nombre_tabla}*"
```

---

## Paso 3 — Sincronizar SERVICES.md

Listar todos los servicios:
```bash
cd /Users/andyb/Documents/seed-app/seed-backend && find app/Services -name "*.php" | sort
```

Para cada servicio encontrado:
1. Leer el archivo completo
2. Extraer: namespace, archivo, responsabilidad (del docblock si existe, o inferir)
3. Para cada método público: firma, tipo de retorno, descripción de una línea
4. Comparar con `.claude/context/SERVICES.md`
5. Agregar servicios faltantes o corregir firmas que hayan cambiado

---

## Paso 4 — Actualizar timestamp

Actualizar la línea `Last updated:` en los tres archivos con la fecha actual:
```bash
cd /Users/andyb/Documents/seed-app/seed-backend && date +%Y-%m-%d
```

---

## Paso 5 — Reporte

```
Context sync backend — {fecha}

ENDPOINTS.md:  {N adiciones / N correcciones / sin cambios}
MODELS.md:     {N adiciones / N correcciones / sin cambios}
SERVICES.md:   {N adiciones / N correcciones / sin cambios}

Cambios:
- {archivo}: {qué se agregó/corrigió}
```

Si todo estaba actualizado:
```
Todos los registros de contexto están al día. Sin cambios.
```
