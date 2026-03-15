# Anthropofilia — Project Context for Claude Code

## Project Overview

Blog personal y CMS para **Ana López Sampedro**, licenciada en Filosofía por la USC.
El blog publica reflexiones filosóficas, ensayos y material educativo.

- **URL de producción:** https://anthropofilia.com
- **Hosting:** Hostinger — `public_html/` es la document root en producción
- **Entorno local:** Docker en `http://localhost:8080` (phpMyAdmin: `http://localhost:8081`)
- **REGLA CRÍTICA:** Nunca reestructurar `public_html/` — es la raíz del servidor en Hostinger.
  Mover archivos de esa carpeta rompe la web en producción de inmediato.

---

## Tech Stack

| Capa | Tecnología |
|------|-----------|
| Backend | PHP ≥ 8.0, PDO, `declare(strict_types=1)` en todos los archivos |
| Base de datos | MariaDB 10.4 (Docker) / MySQL (Hostinger) |
| Servidor web | Apache 2.4, mod_rewrite |
| Dependencias PHP | `ezyang/htmlpurifier ^4.19`, `phpmailer/phpmailer ^7.0`, `vlucas/phpdotenv ^5.6` |
| Autoloading | Composer PSR-4: `App\` → `app/` |
| Editor de contenido | TinyMCE self-hosted (`public_html/js/tinymce/`) + plugin propio `postembed` |
| Frontend | Vanilla JS (sin frameworks), CSS modular con custom properties |
| Entorno local | Docker Compose (`ops/docker-compose.yml`) |

---

## Architecture

### Estructura dual

El proyecto mezcla OOP y procedural de forma deliberada:

```
proyecto/
├── app/                    # Código OOP (autoloaded via Composer)
│   ├── Models/             # Acceso a datos (PDO estático o por instancia)
│   ├── Security/           # SecurityManager (auth, CSRF, CSP, sanitización)
│   └── Helpers/            # Funciones globales + procesadores de contenido
├── public_html/            # Document root — scripts procedurales de entrada
│   ├── init.php            # Bootstrap de toda la aplicación
│   ├── index.php, post.php, pagina.php, ...
│   ├── css/, js/           # Assets estáticos
│   └── api/                # Endpoints JSON para acciones AJAX
├── resources/views/        # Plantillas parciales (header, footer, sidebar)
├── admin/                  # Helpers y fragmentos del panel de administración
├── config/                 # Configuración (solo config/database.php está activo)
└── ops/                    # Docker y herramientas de despliegue
```

### Flujo de una petición

```
URL → Apache (.htaccess mod_rewrite)
    → public_html/[pagina].php
        → require_once 'init.php'           # Bootstrap
            → vendor/autoload.php           # Composer PSR-4
            → vlucas/phpdotenv (.env)       # Variables de entorno
            → Database::getConnection()     # PDO singleton
            → new SecurityManager(...)      # Auth, CSRF, CSP, rate limit
            → $security->boot()             # Aplica headers HTTP de seguridad
            → admin/lib/settings.php        # get_setting() / set_setting()
            → app/Helpers/functions.php     # e(), url(), format_date_es(), etc.
        → Lógica de la página (PDO + Models)
        → require_once header.php           # <!DOCTYPE html> ... <main>
        → HTML específico de la página
        → require_once footer.php           # </main> ... </html> + sidebar condicional
```

### Helpers globales clave (`app/Helpers/functions.php`)

| Función | Uso |
|---------|-----|
| `url(string $path)` | Genera URL absoluta con `$_SERVER['HTTP_HOST']` |
| `e(string $s)` | Alias de `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` |
| `format_date_es($fecha)` | Fecha en formato español ("14 de marzo de 2026") |
| `excerpt($html, $words)` | Extracto sin HTML de N palabras |
| `post_url($post)` | `post.php?slug=...` desde array de post |
| `get_thumbnail_url($url, $html)` | Miniatura de YouTube / imagen destacada |
| `pluralize($n, $sg, $pl)` | Pluralización básica |

---

## Database

### Tablas (9)

| Tabla | Descripción |
|-------|-------------|
| `posts` | Entradas del blog (slug, titulo, contenido, imagen_destacada_url, fecha_publicacion, id_categoria) |
| `paginas` | Páginas estáticas con contenido TinyMCE (slug, titulo, contenido, orden) |
| `categorias` | Categorías de posts (nombre_categoria, slug) |
| `etiquetas` | Etiquetas/tags (nombre_etiqueta) |
| `post_etiquetas` | Relación N:M posts ↔ etiquetas |
| `pagina_posts` | Relación N:M páginas ↔ posts (con orden y tipo_visualizacion: 'card' / 'embebido') |
| `usuarios` | Usuarios del sistema (username, password_hash, rol: administrador / autor / usuario) |
| `rate_limits` | Control de rate limiting por IP + acción |
| `settings` | Configuración clave-valor del tema (columnas `k` / `v`) |
| `sites` | Sitios de interés para el sidebar (nombre, url, activo) |

### Relaciones clave

- `posts.id_categoria` → `categorias.id_categoria`
- `post_etiquetas` → muchos-a-muchos posts ↔ etiquetas
- `pagina_posts` → muchos-a-muchos páginas ↔ posts, con orden numérico y tipo de vista

### Dos archivos de configuración de BD (situación heredada)

- **`config/database.php`** — Activo. Leído por `app/Models/Database.php`. Usa variables `$_ENV`.
- **`public_html/config.php`** — Muerto. Define constantes `DB_DSN`, `DB_USER`, etc. que **nadie usa**.
  Pendiente de eliminar (Fase 0 del refactor).

---

## Critical Rules — NEVER violate these

1. **`public_html/` es la document root de Hostinger.** No renombrarla, no moverla, no añadir un nivel encima.
2. **Prepared statements siempre.** Nunca interpolar variables directamente en SQL. Excepción conocida en `categoria.php` (deuda técnica, no replicar).
3. **CSRF en todos los formularios POST.** Usar `$security->csrfField()` en el form y `$security->requireValidCsrf()` en el handler.
4. **`SecurityManager::requireLogin()`** es el patrón estándar de autenticación. No usar `!isset($_SESSION['id_usuario'])` directamente (patrón legacy que omite idle timeout y rotación de session ID).
5. **Nunca hacer commit de `.env` ni credenciales.** El `.env` está en `.gitignore`.
6. **Sanitizar HTML con `$security->sanitizeHTML()`** (HTMLPurifier) antes de persistir o renderizar contenido de usuario.
7. **`url()` para todas las URLs internas**, nunca hardcodear rutas.

---

## Known Technical Debt (to fix, not replicate)

Estos problemas están documentados. Al editar código existente, no replicarlos en código nuevo.

| ID | Problema | Severidad |
|----|---------|-----------|
| C-01 | `public_html/app/Models/PaginaPost.php` — duplicado exacto de `app/Models/PaginaPost.php`. Nunca cargado por Composer (ruta incorrecta). Pendiente eliminar. | Crítico |
| C-02 | `public_html/config.php` — constantes DB que nadie usa. Código muerto. Pendiente eliminar. | Crítico |
| C-03 | `actualizar_post.php` no actualiza etiquetas al editar un post (se pierden silenciosamente). | Crítico |
| C-04 | `etiqueta.php` usa `require` sin `__DIR__`, URLs con `id=` en vez de slug, e incluye el sidebar fuera del layout. | Crítico |
| C-05 | `SecurityManager::abort()` busca páginas de error en `resources/errors/` pero están en `resources/views/errors/`. Siempre cae al fallback de texto plano. | Crítico |
| C-06 | `guardar_personalizacion.php`: la acción de reset se dispara con `GET ?reset=1` sin verificación CSRF. | Crítico (seguridad) |
| I-01 | ~~Stubs vacíos: `AuthService`, `ImageService`, `MailService`, `SettingsService`.~~ `ImageService` implementado (Fase 3). Pendientes: `AuthService`, `MailService`, `SettingsService`. | Parcial |
| I-02 | ~~Generación de slug duplicada en 4 lugares.~~ Páginas unificadas con `Page::slugify()` y JS compartido `slugify.js` (Fase 6). Pendientes: `guardar_post.php` (slug inline), `Category.php` (tiene su propio `generateSlug()`). | Parcial |
| I-03 | ~~Configuración de TinyMCE copiada en 3 archivos.~~ Resuelto — centralizado en `js/tinymce-config.js` (Fase 6). | Resuelto |
| I-04 | ~~CSS inline masivo en 4 archivos de gestión.~~ Resuelto — movido a `css/admin/` (Fase 5). | Resuelto |
| I-05 | ~~Dos sistemas de personalización: `admin.php` (legacy) y `personalizar.php` (moderno).~~ Resuelto — `admin.php` eliminado (Fase 7). | Resuelto |
| I-06 | ~~`Page.php` es casi un stub.~~ Modelo completo con CRUD, slug y validación. Pendiente: migrar `guardar_pagina.php` y `actualizar_pagina.php` para usar `Page::create()`/`Page::update()` en vez de SQL directo. | Parcial | Resuelto
| I-07 | `init.php` tiene `display_errors=1` hardcodeado antes de leer `APP_ENV`, por lo que siempre expone errores hasta que se sobreescribe. | Importante |
| I-08 | `enviar_contacto.php` tiene `SMTPDebug = 2` activo en producción (logs SMTP visibles). | Importante | Resuelto
| m-01 | `feed.php` usa `$GLOBALS['baseUrl']` (es variable local en init.php, no global). | Menor |
| m-02 | ~~`editar_post.php` referencia `assets/css/styles.css` (ruta que no existe).~~ Resuelto — unificado a `css/style.css` vía `tinymce-config.js` (Fase 6). | Resuelto |


---

## Refactor Plan (fases pendientes)

| Fase | Objetivo | Estado |
|------|---------|--------|
| **Fase 0** | Eliminar duplicados y código muerto; corregir bugs activos (C-01 a C-06) | LISTO |
| **Fase 1** | Completar `app/Models/` — Page CRUD, Tag CRUD | LISTO |
| **Fase 2** | Unificar patrón de autenticación — implementar `AuthService` | LISTO |
| **Fase 3** | Extraer lógica de subida de imágenes a `ImageService` | LISTO |
| **Fase 4** | Implementar `MailService` (encapsular PHPMailer) y `SettingsService` | ANULADA HASTA QUE EXISTA UN SEGUNDO CASO DE USO |
| **Fase 5** | Eliminar CSS inline — mover a `css/admin/` | Completada |
| **Fase 6** | Extraer configuración TinyMCE a JS compartido; unificar función `slugify` | Completada |
| **Fase 7** | Eliminar `admin.php` legacy; consolidar en `personalizar.php` | Completada |

---

## Code Conventions

- **Idioma:** Español para nombres de variables, comentarios, strings de UI y mensajes de error al usuario. Inglés para mensajes de commit.
- **PHP:** Estilo PSR-12. `declare(strict_types=1)` en todos los archivos. Type hints donde sea posible.
- **CSS:** Nomenclatura BEM-like. Organizado en capas: `base/` → `layout/` → `components/` → `pages/` → `admin/`. Custom properties en `variables.css`.
- **Commits:** Inglés, imperativo, descriptivo. Ejemplo: `fix: update post tags on edit`, `feat: add image resize to ImageService`.
- **Seguridad:** Usar `e()` para todo output de variables en HTML. Prepared statements para toda consulta SQL. `$security->sanitizeHTML()` para contenido del editor.

---

## Git Workflow

| Rama | Propósito |
|------|-----------|
| `main` | Estable, refleja producción |
| `develop` | Desarrollo activo — rama base para nuevas features |
| `rama-cambios-pagina-post` | Versión actualmente desplegada en Hostinger |
| `hostinger_v1`, `hostinger_v2` | Snapshots históricos de versiones anteriores |

**Flujo estándar:**
1. Crear rama feature desde `develop`: `git checkout -b fix/nombre-del-fix develop`
2. Desarrollar y testear en Docker local
3. Commit con mensaje descriptivo en inglés
4. Merge a `develop`
5. Cuando `develop` es estable, merge a `main` y desplegar en Hostinger

---

## Local Development

```bash
# Levantar entorno completo
cd ops && docker compose up -d

# Web:        http://localhost:8080
# phpMyAdmin: http://localhost:8081  (usuario: root / pass: root)

# Ver logs de PHP/Apache
docker compose logs -f web

# Bajar el entorno
docker compose down
```

La base de datos se inicializa automáticamente desde `database/blogdb.sql` al primer `docker compose up`.

---

## Session Protocol

Cada sesión de trabajo sigue este patrón:

1. **Leer** el archivo objetivo antes de proponer cualquier cambio
2. **Identificar** exactamente qué cambia y dónde va
3. **Implementar** el cambio mínimo necesario (no sobre-ingeniería)
4. **Verificar** localmente con Docker (`localhost:8080`)
5. **Commit** a `develop` con mensaje descriptivo en inglés

## Interaction Mode

ALWAYS work in "snippet mode" unless explicitly told otherwise:
- Show the exact code change (snippet) with file path and location
- Explain WHAT changes and WHY before the person applies it
- Wait for confirmation before moving to the next change
- NEVER modify multiple files in a single step without asking
- If a task requires more than 3 file changes, present a numbered plan first and execute one step at a time
