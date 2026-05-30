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
| `slugify($text)` | Slug limpio desde texto libre (transliterá acentos/ñ, minúsculas, guiones, máx. 150) |

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
| `usuarios` | Usuarios del sistema (nombre_usuario, email [UNIQUE, usado para login], contrasena_hash, rol: administrador / autor / usuario, fecha_registro). `nombre_usuario` almacena el nombre completo (sin UNIQUE). |
| `rate_limits` | Control de rate limiting por IP + acción |
| `settings` | Configuración clave-valor del tema (columnas `k` / `v`) |
| `sites` | Sitios de interés para el sidebar (nombre, url, activo) |

### Relaciones clave

- `posts.id_categoria` → `categorias.id_categoria`
- `post_etiquetas` → muchos-a-muchos posts ↔ etiquetas
- `pagina_posts` → muchos-a-muchos páginas ↔ posts, con orden numérico y tipo de vista

### Dos archivos de configuración de BD (situación heredada)

- **`config/database.php`** — Activo. Leído por `app/Models/Database.php`. Usa variables `$_ENV`.
- ~~**`public_html/config.php`** — Muerto. Define constantes `DB_DSN`, `DB_USER`, etc. que **nadie usa**.~~
  Eliminado (Fase 0).

### Autenticación y registro (Fase 8)

- **Login por email** — `procesar_login.php` busca por `WHERE email = ?`. El campo `nombre_usuario` ya no es identificador de login.
- **Registro público** — `registro.php` + `procesar_registro.php`. Campos: nombre, apellido1, apellido2 (opcional), email, contraseña (x2), checkbox RGPD. Se concatena nombre completo en `nombre_usuario`. Rol siempre `usuario`.
- **Protección de rol** — Todas las páginas de admin usan `$security->requireRole(['administrador', 'autor'])` después de `requireLogin()`. Usuarios con rol `usuario` reciben 403.
- **Dashboard por rol** — `dashboard.php` redirige a `dashboard_usuario.php` si el rol es `usuario`. La navegación del header muestra "Panel de Control" para admin/autor y "Mi Cuenta" para usuario estándar.

---

## Critical Rules — NEVER violate these

1. **`public_html/` es la document root de Hostinger.** No renombrarla, no moverla, no añadir un nivel encima.
2. **Prepared statements siempre.** Nunca interpolar variables directamente en SQL. Excepción conocida en `categoria.php` (deuda técnica, no replicar).
3. **CSRF en todos los formularios POST.** Usar `$security->csrfField()` en el form y `$security->requireValidCsrf()` en el handler.
4. **`SecurityManager::requireLogin()`** es el patrón estándar de autenticación. No usar `!isset($_SESSION['id_usuario'])` directamente (patrón legacy que omite idle timeout y rotación de session ID).
5. **Nunca hacer commit de `.env` ni credenciales.** El `.env` está en `.gitignore`.
6. **Sanitizar HTML con `$security->sanitizeHTML()`** (HTMLPurifier) antes de persistir o renderizar contenido de usuario.
7. **`url()` para todas las URLs internas**, nunca hardcodear rutas.
8. **`$security->requireRole(['administrador', 'autor'])`** en toda página de administración, siempre después de `requireLogin()`. Desde que existe registro público, `requireLogin()` solo ya no es suficiente.

---

## Known Technical Debt (to fix, not replicate)

Estos problemas están documentados. Al editar código existente, no replicarlos en código nuevo.

| ID | Problema | Severidad |
|----|---------|-----------|
| C-01 | ~~`public_html/app/Models/PaginaPost.php` — duplicado exacto de `app/Models/PaginaPost.php`. Nunca cargado por Composer (ruta incorrecta). Pendiente eliminar.~~ Eliminado (Fase 0). | Resuelto |
| C-02 | ~~`public_html/config.php` — constantes DB que nadie usa. Código muerto. Pendiente eliminar.~~ Eliminado (Fase 0). | Resuelto |
| C-03 | ~~`actualizar_post.php` no actualiza etiquetas al editar un post (se pierden silenciosamente).~~ Corregido — gestiona etiquetas con transacción (Fase 0). | Resuelto |
| C-04 | ~~`etiqueta.php` usa `require` sin `__DIR__`, URLs con `id=` en vez de slug, e incluye el sidebar fuera del layout.~~ Corregido (Fase 0). | Resuelto |
| C-05 | ~~`SecurityManager::abort()` busca páginas de error en `resources/errors/` pero están en `resources/views/errors/`.~~ Ruta corregida a `resources/views/errors/` (Fase 0). | Resuelto |
| C-06 | ~~`guardar_personalizacion.php`: la acción de reset se dispara con `GET ?reset=1` sin verificación CSRF.~~ Reset migrado a POST con CSRF (Fase 0). | Resuelto |
| I-01 | ~~Stubs vacíos: `AuthService`, `ImageService`, `MailService`, `SettingsService`.~~ `ImageService` implementado (Fase 3). `AuthService.php` y `SettingsService.php` son stubs vacíos (0 bytes), anulados — la lógica de auth vive en `SecurityManager` (Fase 2) y no hay segundo caso de uso para settings. `MailService.php` no existe (nunca se creó). | Resuelto (decisión consciente) |
| I-02 | ~~Generación de slug duplicada en 4 lugares.~~ Centralizado en helper `slugify()` de `app/Helpers/functions.php` (mapa explícito del español + iconv). `Page::slugify()`, `Category::generateSlug()` y `guardar_post.php` delegan en él. Solo queda fuera `TocGenerator::generateSlug()` porque genera anchors de encabezados (propósito distinto, no se unifica a propósito). | Resuelto (decisión consciente) |
| I-03 | ~~Configuración de TinyMCE copiada en 3 archivos.~~ Resuelto — centralizado en `js/tinymce-config.js` (Fase 6). | Resuelto |
| I-04 | ~~CSS inline masivo en 4 archivos de gestión.~~ Resuelto — movido a `css/admin/` (Fase 5). | Resuelto |
| I-05 | ~~Dos sistemas de personalización: `admin.php` (legacy) y `personalizar.php` (moderno).~~ Resuelto — `admin.php` eliminado (Fase 7). | Resuelto |
| I-06 | ~~`Page.php` es casi un stub.~~ Modelo completo con CRUD, slug y validación. ~~Pendiente: migrar `guardar_pagina.php` y `actualizar_pagina.php` para usar `Page::create()`/`Page::update()` en vez de SQL directo.~~ Ambos migrados — auditoría confirmada. | Resuelto |
| I-07 | ~~`init.php` tiene `display_errors=1` hardcodeado antes de leer `APP_ENV`, por lo que siempre expone errores hasta que se sobreescribe.~~ Resuelto — detección temprana de entorno con default seguro (prod = display off). Además, los handlers de excepción y shutdown ahora respetan el entorno: detalle solo en dev, mensaje genérico + log en prod (cerraba una fuga mayor: volcaban el stack trace completo en producción ignorando APP_ENV). | Resuelto |
| I-08 | ~~`enviar_contacto.php` tiene `SMTPDebug = 2` activo en producción (logs SMTP visibles).~~ Resuelto. | Resuelto |
| I-09 | ~~`crear_post.php` no incluye `tinymce-config.js` — llama a `initTinyMCE()` sin haberla cargado. El editor nunca se inicializa.~~ Resuelto — añadido script + nonce. | Resuelto |
| I-10 | ~~`User.php` no tiene `declare(strict_types=1)`. Viola convención del proyecto.~~ Resuelto — añadido antes del namespace. | Resuelto |
| I-11 | ~~`login.php` tiene checkbox "Mantener sesión" que no hace nada (nunca se lee en `procesar_login.php`).~~ Resuelto — checkbox eliminado. "Remember me" real (tokens persistentes) movido a Future Work. | Resuelto |
| I-12 | ~~`procesar_login.php` redirige a `login.php?status=error` en catch de BD, pero `$messages` en `login.php` no tiene clave `'error'`. No muestra mensaje al usuario.~~ Resuelto — añadida clave `'error'` con mensaje genérico. | Resuelto |
| I-13 | Enumeración de email en registro: `status=email_exists` confirma si un email está registrado. Decisión consciente: se mantiene el mensaje claro para no degradar la experiencia de usuarios legítimos que se registran dos veces por olvido. Modelo de amenaza: blog público de filosofía sin datos sensibles, sin transacciones, sin mensajería privada. Mitigado con rate limiting en el endpoint de registro y logging de intentos contra emails existentes. Reconsiderar si el proyecto evoluciona hacia funcionalidades con datos sensibles. | Decisión consciente |
| B-01 | ~~`guardar_post.php` genera slugs incorrectos para títulos con acentos o ñ. Las letras acentuadas se eliminan en vez de transliterarse. Ejemplo: "Filosofía española" → `filosofa-espaola` (incorrecto), debería ser `filosofia-espanola`. Relacionado con I-02.~~ Resuelto — usa `slugify()` centralizado. | Resuelto |
| m-01 | ~~`feed.php` usa `$GLOBALS['baseUrl']` (es variable local en init.php, no global).~~ Resuelto — usa `$baseUrl` directo con fallback a `url('')`. | Resuelto |
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
| **Fase 8** | Sistema de registro público + login por email + protección de rol en páginas admin | Completada |

---

## Recent Features

- **Plugins TinyMCE** — `postembed` (insertar posts del blog) y `externalembed` (YouTube, Vimeo, Genially, Calameo, Google Drive, PDF, iframe genérico) con auto-detección de plataformas.
- **Modernización visual** — Cards con sombras y hover, hero post, tipografía Playfair Display con `clamp()`, grid responsive, sidebar transparente.
- **Páginas legales** — `aviso-legal.php`, `privacidad.php`, `cookies.php` y página dedicada `accesibilidad.php`.
- **Accesibilidad** — Skip-to-content, focus-visible mejorado, `prefers-reduced-motion`, toggle de interlineado con persistencia en `localStorage` (FAB en esquina).
- **Tabla de contenidos automática** — `TocGenerator.php` + `toc.js` para posts con 3+ encabezados.
- **Sistema de búsqueda** — Página pública `search.php` y endpoint admin `api/post_search.php` con autocompletado, implementados sobre `Post::search()` con prepared statements.
- **Registro público + login por email** — Formulario de registro con validación, protección CSRF, rate limiting y control de acceso por rol en páginas admin (Fase 8).

---

## Future Work

- **Sistema de analíticas propio** — Tabla `visitas`, tracking de pageviews sin cookies de terceros, mini dashboard en admin. Reemplazo de dependencias en servicios externos.
- **Suite de tests PHPUnit** — La carpeta `tests/` con subdirectorios `Feature/` y `Unit/` ya existe como placeholder. Falta `phpunit.xml` y los primeros tests. Empezar por los modelos críticos (Post, Page, User).
- **Sistema de comentarios** — Siguiente funcionalidad planificada tras el registro. Da sentido al rol `usuario` y alimenta estadísticas para `dashboard_usuario.php`.
- **"Remember me" (login persistente)** — Checkbox del login pendiente de implementar con patrón seguro: tabla de tokens persistentes (selector + validador hasheado), cookie de larga duración, validación y rotación en cada sesión nueva, expiración y limpieza. Valor real para Ana, que entra de forma esporádica.

---

## Claude Code Subagents

Subagentes especializados disponibles en `.claude/agents/`:

| Agente | Propósito |
|--------|-----------|
| `architect.md` | Decisiones de arquitectura: organización de código, modelos, planificación de features y refactors. |
| `css-reviewer.md` | CSS: extracción de estilos inline, revisión de arquitectura, responsive design y debugging visual. Conoce el sistema de capas y BEM del proyecto. |
| `db-guardian.md` | Base de datos: queries, schema, migraciones y consistencia entre desarrollo y producción. |
| `php-developer.md` | Desarrollo PHP: modelos, helpers, scripts, bugs y features siguiendo las convenciones del proyecto. |
| `security-auditor.md` | Seguridad: CSRF, validación de inputs, CSP, autenticación, rate limiting y formularios públicos. |

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
| `hostinger_v1`, `hostinger_v2` | Snapshots históricos de versiones anteriores, pendientes de eliminar |

**Flujo estándar:**
1. Crear rama feature desde `develop`: `git checkout -b fix/nombre-del-fix develop`
2. Desarrollar y testear en Docker local
3. Commit con mensaje descriptivo en inglés
4. Merge a `develop`
5. Cuando `develop` es estable, merge a `main` y desplegar en Hostinger (`git pull origin main` en producción)

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

### Read-only sessions

En muchas sesiones, el trabajo de Claude Code se limita a leer, analizar y proponer snippets. El usuario aplica los cambios manualmente a archivos `.php`, `.js`, `.css`, `.sql`, etc. La única excepción es `CLAUDE.md`, que Claude Code sí puede actualizar directamente cuando se le indique. Esto debe asumirse por defecto: si una tarea requiere modificar código funcional, Claude Code propone el snippet y espera; no toca el archivo.
