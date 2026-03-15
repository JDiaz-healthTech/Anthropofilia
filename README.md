# Anthropofilia

**CMS ligero en PHP para divulgación educativa y cultural.**

Diseñado para que una profesora de filosofía publique materiales didácticos — posts, páginas estáticas con contenido interactivo (Genially, Calameo, YouTube), y recursos organizados por categorías y etiquetas — sin depender de plataformas de terceros.

🔗 **[anthropofilia.es](https://anthropofilia.es)**

---

## Stack

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.0+, PDO, MariaDB 10.4+ |
| Frontend | HTML5, CSS3 (Grid, Flexbox, Custom Properties), JS ES6+ |
| Editor | TinyMCE 6 (self-hosted) |
| Seguridad | SecurityManager propio (CSRF, rate limiting, CSP, HTMLPurifier) |
| Imágenes | ImageService (validación MIME, resize, conversión WebP) |
| Infraestructura | Docker (desarrollo), Hostinger (producción), Git |

---

## Funcionalidades

### Contenido público
- Posts con contenido enriquecido, imágenes destacadas y embeds multimedia
- Páginas estáticas con índice de contenidos autogenerado (TOC)
- Sistema de relación páginas ↔ posts (embebidos o cards)
- Categorías, etiquetas y archivo mensual
- Buscador con paginación
- Feed RSS y sitemap XML
- Formulario de contacto (CSRF + rate limiting + honeypot + PHPMailer)

### Panel de administración
- Autenticación con roles (autor/administrador) y sesiones seguras
- CRUD completo de posts, páginas, categorías y sitios de interés
- Editor TinyMCE con subida de imágenes, YouTube/Vimeo embed y plugin de inserción de posts
- Personalización visual (imagen de cabecera, overlay, historial de fondos)
- Dashboard con estadísticas y acciones rápidas

### Accesibilidad
- Alto contraste y modo oscuro
- Ajuste de tamaño de fuente
- Navegación por teclado
- FAB de accesibilidad flotante

### Seguridad
- CSRF en todos los formularios y APIs
- Rate limiting por IP/acción
- Validación MIME real en subidas (no por extensión)
- Content Security Policy centralizada
- Prepared statements (PDO) en todas las consultas
- HTMLPurifier para sanitización de contenido

---

## Arquitectura

```
app/
├── Models/          # Post, Page, Tag, Category, Sites, PaginaPost
├── Security/        # SecurityManager (auth, CSRF, CSP, rate limit)
├── Services/        # ImageService (resize, WebP, validación)
└── Helpers/         # functions.php, PostEmbedProcessor, TocGenerator

public_html/         # Document root (Apache)
├── css/             # Sistema de capas: base → layout → components → pages → admin
├── js/              # tinymce-config.js, slugify.js, lightbox, accessibility
├── api/             # Endpoints AJAX (búsqueda, relación páginas-posts)
└── uploads/         # Imágenes organizadas por año/mes

resources/views/     # Plantillas parciales (header, footer, sidebar, errores)
ops/                 # Docker + scripts de mantenimiento
```

---

## Desarrollo local

```bash
# Clonar
git clone https://github.com/JDiaz-healthTech/Anthropofilia.git
cd Anthropofilia

# Levantar entorno Docker
cd ops && docker-compose up -d

# Configurar
cp .env.example .env   # Editar credenciales DB y SMTP
composer install

# Acceder
http://localhost:8080
```

---

## Créditos

Desarrollado por **Julio Díaz López** como proyecto de aprendizaje y portfolio.

Contenido editorial y dirección pedagógica: **Ana López Sampedro** — profesora de filosofía con más de tres décadas de experiencia docente.
