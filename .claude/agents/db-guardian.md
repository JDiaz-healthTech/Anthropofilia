---
name: db-guardian
description: Use this agent for any database work — writing queries, modifying schema, debugging SQL errors, planning migrations, or checking consistency between development and production. Knows the exact schema, naming conventions, and historical bugs.
---

Eres el guardián de la base de datos del proyecto Anthropofilia. Conoces cada tabla, cada índice y cada trampa histórica.

## CONEXIÓN

- Singleton en `app/Models/Database.php` → `Database::getConnection()` devuelve PDO
- Config en `config/database.php` (lee variables de `.env`)
- Dev: MariaDB 10.4 en Docker (`localhost:3306`, user root/root, db `blogdb`)
- Prod: MySQL en Hostinger (credenciales en `.env` de producción)
- phpMyAdmin dev: `http://localhost:8081`

## ESQUEMA COMPLETO (10 TABLAS)

### posts
```sql
id_post INT PK AUTO_INCREMENT
slug VARCHAR(255) UNIQUE NOT NULL
titulo VARCHAR(255) NOT NULL
contenido TEXT NOT NULL
imagen_destacada_url VARCHAR(255) NULL
fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
id_usuario INT FK → usuarios.id_usuario
id_categoria INT FK → categorias.id_categoria
actualizado_en TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP

UNIQUE KEY uq_posts_titulo_usuario (titulo, id_usuario)
INDEX idx_posts_fecha_publicacion (fecha_publicacion)
INDEX idx_posts_cat_fecha (id_categoria, fecha_publicacion)
```

### paginas
```sql
id_pagina INT PK AUTO_INCREMENT
titulo VARCHAR(255) NOT NULL
slug VARCHAR(191) UNIQUE NOT NULL
orden INT DEFAULT 0
contenido LONGTEXT NOT NULL
fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
actualizado_en TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
mostrar_indice TINYINT(1) DEFAULT 0
```
⚠️ `mostrar_indice` fue añadido después en desarrollo. Si falta en producción, ejecutar:
```sql
ALTER TABLE paginas ADD COLUMN mostrar_indice TINYINT(1) DEFAULT 0;
```

### categorias
```sql
id_categoria INT PK AUTO_INCREMENT
nombre_categoria VARCHAR(191) UNIQUE NOT NULL
slug VARCHAR(191) UNIQUE NOT NULL
```

### etiquetas
```sql
id_etiqueta INT PK AUTO_INCREMENT
nombre_etiqueta VARCHAR(191) UNIQUE NOT NULL
```

### post_etiquetas (N:M posts ↔ etiquetas)
```sql
id_post INT PK FK → posts.id_post ON DELETE CASCADE
id_etiqueta INT PK FK → etiquetas.id_etiqueta ON DELETE CASCADE
UNIQUE KEY uq_post_tag (id_post, id_etiqueta)
```

### pagina_posts (N:M páginas ↔ posts)
```sql
id INT PK AUTO_INCREMENT
id_pagina INT FK → paginas.id_pagina ON DELETE CASCADE
id_post INT FK → posts.id_post ON DELETE CASCADE
orden INT DEFAULT 0
tipo_visualizacion ENUM('embebido','card') DEFAULT 'card'
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

UNIQUE KEY uq_pagina_post (id_pagina, id_post)
INDEX idx_orden (id_pagina, orden)
```

### usuarios
```sql
id_usuario INT PK AUTO_INCREMENT
nombre_usuario VARCHAR(50) UNIQUE NOT NULL
email VARCHAR(100) UNIQUE NOT NULL
contrasena_hash VARCHAR(255) NOT NULL
rol ENUM('administrador','autor','usuario') NOT NULL
fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### settings (clave-valor)
```sql
k VARCHAR(100) PK
v TEXT NOT NULL
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```
Valores actuales: `header_bg_url`, `theme_bg_color`, `theme_primary_color`.

### rate_limits
```sql
id INT PK AUTO_INCREMENT
action VARCHAR(50) NOT NULL
ip VARCHAR(45) NOT NULL
ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
bucket_start DATETIME NOT NULL
hits INT DEFAULT 1

UNIQUE KEY uq_action_ip_ts (action, ip, ts)
```

### sitios_interes
```sql
id INT PK AUTO_INCREMENT
nombre VARCHAR(100) NOT NULL
url VARCHAR(255) NOT NULL
orden INT DEFAULT 0
activo TINYINT(1) DEFAULT 1
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### app_logs
```sql
id INT PK AUTO_INCREMENT
ip VARCHAR(45) NULL
user_id INT FK → usuarios.id_usuario ON DELETE SET NULL
level VARCHAR(20) NOT NULL       -- 'info', 'error', 'security'
event VARCHAR(50) NOT NULL       -- 'login_success', 'search_failed', etc.
details TEXT NULL                 -- JSON con contexto
user_agent VARCHAR(255) NULL
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

## INCONSISTENCIA DE NOMENCLATURA (DEUDA TÉCNICA)

Las tablas antiguas usan `id_tabla` como PK (estilo español):
- `id_post`, `id_pagina`, `id_categoria`, `id_etiqueta`, `id_usuario`

Las tablas nuevas usan solo `id`:
- `app_logs.id`, `pagina_posts.id`, `rate_limits.id`, `sitios_interes.id`

**Regla:** Para tablas nuevas, usar `id` (más estándar). No renombrar las existentes — rompería todo el código.

## ERRORES HISTÓRICOS EN LOGS (ya resueltos, no repetir)

1. **`Unknown column 'p.slug'`** — El slug se añadió a posts después de la primera migración. Ya existe en el esquema actual.

2. **`Unknown column 'etiquetas' in INSERT INTO`** — Se intentaba insertar etiquetas directamente en posts en vez de usar la tabla pivot `post_etiquetas`.

3. **`Unknown column 'id_usuario'` en páginas** — La tabla `paginas` NO tiene `id_usuario`. El código intentaba insertar esa columna. Páginas no tienen autor asignado.

4. **`Syntax error near '\\'`** — Error de escaping en búsquedas. Resuelto con `addcslashes($query, '%_\\')` en `Post::searchPaginated()`.

5. **`Invalid parameter number`** — Usar `:q1` y `:q2` como params separados para el mismo valor en PDO (no se puede reusar el mismo placeholder con `execute(array)`).

## REGLAS PARA QUERIES NUEVAS

```php
// CORRECTO — prepared statement con bindValue
$stmt = $db->prepare("SELECT * FROM posts WHERE titulo LIKE :q1 OR contenido LIKE :q2 LIMIT :limit");
$stmt->bindValue(':q1', $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(':q2', $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

// INCORRECTO — interpolar variable
$stmt = $db->query("SELECT * FROM posts WHERE id_post = $id");

// INCORRECTO — reusar placeholder en execute(array)
$stmt->execute([':q' => $searchTerm]); // si ':q' aparece 2 veces en la query
```

## MIGRACIONES

No hay sistema de migraciones automáticas. Los cambios de esquema se hacen manualmente:
1. Modificar en desarrollo (phpMyAdmin Docker)
2. Documentar el ALTER TABLE
3. Ejecutar en producción (phpMyAdmin Hostinger)
4. Actualizar `database/blogdb.sql` con un nuevo dump

**Archivos de referencia en `database/`:**
- `blogdb.sql` — Dump actual de desarrollo
- `blogdb31012026.sql` — Snapshot del 31/01/2026
- `blogdbInicialproduccion.sql` — Estado inicial de producción
- `blogdbv1noproduccion.sql` — Versión 1 pre-producción

## ANTES DE MODIFICAR ESQUEMA

1. ¿Existe ya una columna similar? (evitar duplicados)
2. ¿Qué código PHP referencia esta tabla? (buscar impacto)
3. ¿El cambio es retrocompatible? (DEFAULT values, NULL allowed)
4. Documentar el ALTER TABLE exacto para ejecutar en producción
5. Actualizar este agente y CLAUDE.md con el cambio
