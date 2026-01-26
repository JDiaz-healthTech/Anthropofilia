# Anthropofilia

Sitio web/blog desarrollado en **PHP + MySQL (PDO)** con sistema de administración privado y enfoque en **accesibilidad** y **rendimiento**.  
Este repositorio forma parte de mi portfolio.

---

## ✨ Funcionalidades principales

### Front (público)
- Portada con listado de posts y paginación.
- Post individual con imagen destacada, contenido enriquecido y **lightbox** para imágenes.
- Páginas estáticas (Historia da Filosofía, Ética, Acerca de mí…).
- Categorías y archivo mensual de posts.
- Buscador integrado.
- Formulario de contacto con:
  - CSRF token.
  - Rate limiting.
  - Honeypot anti-spam.
  - PRG (Post → Redirect → Get).
- Aside con:
  - Archivo mensual.
  - Sitios de interés con mini-foto/favicon.

### Administración (privado)
- Autenticación con roles (autor / administrador).
- Panel de control.
- Gestión de posts:
  - Crear, editar, eliminar posts con editor TinyMCE + subida de imágenes.
- Gestión de páginas:
  - Crear, editar, eliminar páginas estáticas.
- Logout seguro (invalidación de sesión).

### Infraestructura y seguridad
- **SecurityManager** con:
  - CSRF tokens.
  - Rate limiting por IP/acción.
  - Validación de entradas y subidas.
- Subida de imágenes:
  - Validación MIME y dimensiones.
  - Redimensionado automático.
  - Conversión a **WebP** si es posible.
- Estilos:
  - Tema responsive con `style.css`.
  - `lightbox.js` para proyección de imágenes.
  - `accessibility.js` para alto contraste y fuentes grandes.

### Legales
- Plantillas incluidas para:
  - Política de Privacidad.
  - Política de Cookies.
  - Aviso Legal.

---

## 🚀 Tecnologías
- PHP 8+ (PDO, sesiones)
- MySQL/MariaDB
- HTML5, CSS3
- JavaScript ES6+
- TinyMCE (editor enriquecido)

---

## 📂 Estructura de directorios (resumen)

- /index.php → portada
- /post.php → post individual
- /pagina.php → página estática
- /categoria.php → listado por categoría
- /contacto.php → formulario de contacto
- /enviar_contacto.php → handler de contacto
- /dashboard.php → admin posts
- /gestionar_paginas.php → admin páginas
- /upload_image.php → subida de imágenes (TinyMCE)
- /public/js/lightbox.js → proyección de imágenes
- /style.css → estilos principales


## 👩‍💻 Créditos
- Desarrollado por Julio Díaz López (Editora, usuaria administradora del contenido: Ana López Sampedro).  
- Proyecto con fines de aprendizaje y portfolio.