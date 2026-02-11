<?php
// editar_pagina.php
require_once __DIR__ . '/init.php';

use App\Models\PaginaPost;

// 1) Auth - Solo admins
$security->requireLogin();
$security->requireRole(['administrador', 'autor']);

// 2) Obtener y validar id
$pagina_id = (int)$security->cleanInput($_GET['id'] ?? '', 'int');
if ($pagina_id <= 0) {
    http_response_code(400);
    header("Location: gestionar_paginas.php?error=id_invalido");
    exit();
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3) Cargar la página
    $stmt = $pdo->prepare("SELECT id_pagina, titulo, slug, contenido, orden, mostrar_indice FROM paginas WHERE id_pagina = ? LIMIT 1");
    $stmt->execute([$pagina_id]);
    $pagina = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pagina) {
        http_response_code(404);
        header("Location: gestionar_paginas.php?error=not_found");
        exit();
    }

    // 4) Cargar posts relacionados
    $paginaPost = new PaginaPost($pdo);
    $postsRelacionados = $paginaPost->getPostsInPagina($pagina_id);

    // 5) Cargar categorías para el filtro del modal
    $stmt = $pdo->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    http_response_code(500);
die('Error: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
}

$page_title = 'Editar Página';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>
<main class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <a href="<?= url('dashboard.php') ?>">Panel de Control</a>
        <span aria-hidden="true">›</span>
        <a href="<?= url('gestionar_paginas.php') ?>">Gestionar Páginas</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Editar Página</span>
    </nav>

    <h1>Editar página estática</h1>

    <form action="<?= url('actualizar_pagina.php') ?>" method="post" class="form-container" accept-charset="UTF-8">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($security->csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id_pagina" value="<?= (int)$pagina['id_pagina'] ?>">

        <div>
            <label for="titulo">Título</label>
            <input
                type="text" id="titulo" name="titulo"
                required maxlength="150" autocomplete="off"
                value="<?= htmlspecialchars($pagina['titulo'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div>
            <label for="slug">Slug (URL amigable)</label>
            <input
                type="text" id="slug" name="slug"
                required maxlength="191" spellcheck="false" autocapitalize="off" autocomplete="off"
                pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$"
                title="Solo minúsculas, números y guiones (ej: historia-da-filosofia)"
                value="<?= htmlspecialchars($pagina['slug'], ENT_QUOTES, 'UTF-8') ?>">
            <small>Usa minúsculas, sin acentos, separadas por guiones.</small>
        </div>

        <div>
            <label for="contenido">Contenido</label>
            <textarea
                id="contenido" name="contenido" rows="20" maxlength="50000"><?= htmlspecialchars($pagina['contenido'], ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div>
            <label for="orden">Orden en el menú</label>
            <input
                type="number" id="orden" name="orden"
                min="0" max="100" step="1"
                value="<?= (int)($pagina['orden'] ?? 0) ?>">
            <small>Número menor aparece primero. Usa 10, 20, 30... para reorganizar fácilmente después.</small>
        </div>
        <div class="form-group form-group--checkbox">
            <label class="checkbox-label">
                <input type="checkbox" id="mostrar_indice" name="mostrar_indice" value="1"
                    <?= !empty($pagina['mostrar_indice']) ? 'checked' : '' ?>>
                <span class="checkbox-text">Mostrar índice de contenidos</span>
            </label>
            <small>Genera automáticamente un índice basado en los títulos (Título 1, 2, 3) del contenido.</small>
        </div>

        <div style="display:flex; gap:.5rem; align-items:center;">
            <button type="submit">Actualizar página</button>
            <a href="<?= url('gestionar_paginas.php') ?>">Cancelar</a>
        </div>
    </form>

    <!-- ========================================
         SECCIÓN NUEVA: POSTS RELACIONADOS
         ======================================== -->
    <section class="posts-relacionados-manager" style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #ddd;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2>📚 Posts Relacionados (<?= count($postsRelacionados) ?>)</h2>
            <button type="button" id="btnAddPost" class="btn btn-primary">+ Añadir Post</button>
        </div>

        <!-- Lista de posts relacionados -->
        <div id="postsRelacionadosList" class="posts-list">
            <?php if (empty($postsRelacionados)): ?>
                <p class="empty-state" style="padding: 2rem; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">
                    No hay posts relacionados. Haz clic en "Añadir Post" para empezar a construir la narrativa de esta página.
                </p>
            <?php else: ?>
                <?php foreach ($postsRelacionados as $post): ?>
                    <div class="post-item" data-id="<?= $post['id_post'] ?>" data-orden="<?= $post['orden'] ?>">
                        <div class="post-item-header">
                            <span class="drag-handle" title="Arrastrar para reordenar">⋮⋮</span>
                            <div class="post-info">
                                <strong><?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <small>
                                    Categoría: <?= htmlspecialchars($post['nombre_categoria'] ?? 'Sin categoría', ENT_QUOTES, 'UTF-8') ?> |
                                    Publicado: <?= date('d/m/Y', strtotime($post['fecha_publicacion'])) ?>
                                </small>
                            </div>
                            <div class="post-actions">
                                <select class="tipo-viz" data-id="<?= $post['id_post'] ?>">
                                    <option value="card" <?= $post['tipo_visualizacion'] === 'card' ? 'selected' : '' ?>>Card</option>
                                    <option value="embebido" <?= $post['tipo_visualizacion'] === 'embebido' ? 'selected' : '' ?>>Embebido</option>
                                </select>
                                <button type="button" class="btn-remove" data-id="<?= $post['id_post'] ?>" title="Eliminar">✖</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal para añadir posts -->
    <div id="modalAddPost" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Añadir Post a la Página</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="search-container">
                    <input type="text" id="searchPosts" placeholder="Buscar posts..." style="width: 100%; padding: 0.75rem; margin-bottom: 1rem;">

                    <div style="margin-bottom: 1rem;">
                        <label>Filtrar por categoría:</label>
                        <select id="filterCategoria" style="padding: 0.5rem;">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>">
                                    <?= htmlspecialchars($cat['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="searchResults" class="search-results">
                    <p class="loading">Cargando posts...</p>
                </div>

                <div class="modal-footer" style="margin-top: 1rem;">
                    <label>
                        Mostrar como:
                        <select id="tipoVisualizacion">
                            <option value="card">Card</option>
                            <option value="embebido">Embebido</option>
                        </select>
                    </label>
                    <button type="button" id="btnAddSelected" class="btn btn-primary">Añadir Seleccionados</button>
                </div>
            </div>
        </div>
    </div>

</main>
<!-- CSS del modal para insertar posts -->
<link rel="stylesheet" href="<?= url('css/components/post-embed-modal.css') ?>">

<?php
// CSP Nonce para scripts inline
$nonceAttr = ($security->cspNonce())
    ? ' nonce="'.htmlspecialchars($security->cspNonce(), ENT_QUOTES, 'UTF-8').'"'
    : '';
?>

<!-- Plugin de inserción de posts -->
<script src="<?= url('js/tinymce-post-embed.js') ?>"<?= $nonceAttr ?>></script>
<script<?= $nonceAttr ?>>
window.PostEmbedConfig = {
    searchUrl: '<?= url("api/post_search.php") ?>',
    csrfToken: '<?= $security->csrfToken() ?>'
};
</script>

<!-- Configuración de TinyMCE -->
<script<?= $nonceAttr ?>>
if (typeof tinymce !== 'undefined') {
  tinymce.init({
    selector: '#contenido',
    plugins: 'code link lists image media table autoresize postembed',
toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media table | postembed | code',
    menubar: false,
    height: 540,

    block_formats: 'Párrafo=p; Título 1=h2; Título 2=h3; Título 3=h4; Cita=blockquote; Preformateado=pre',
    link_target_list: [{ title: 'Nueva pestaña', value: '_blank' }, { title: 'Misma pestaña', value: '' }],
    rel_list: [{ title: 'Ninguno', value: '' }, { title: 'noopener', value: 'noopener' }, { title: 'nofollow', value: 'nofollow' }],
    default_link_target: '_blank',

    images_upload_url: '<?= url("upload_image.php") ?>',
    images_upload_credentials: true,
    automatic_uploads: true,
    image_caption: true,
    image_dimensions: false,
    image_class_list: [
      { title: 'Por defecto', value: '' },
      { title: 'Ancho completo', value: 'img-wide' }
    ],

    images_upload_handler: function (blobInfo, progress) {
      return new Promise(function(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= url("upload_image.php") ?>');
        xhr.withCredentials = true;
        xhr.setRequestHeader('X-CSRF-Token', '<?= $security->csrfToken() ?>');
        xhr.upload.onprogress = function (e) { if (e.lengthComputable) progress(e.loaded / e.total * 100); };
        xhr.onload = function () {
          if (xhr.status < 200 || xhr.status >= 300) { reject('HTTP ' + xhr.status); return; }
          try {
            var json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location !== 'string') { reject('Respuesta inválida'); return; }
            resolve(json.location);
          } catch (err) { reject('JSON inválido'); }
        };
        xhr.onerror = function () { reject('Error de red'); };
        var formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
      });
    },

    paste_data_images: false,

    content_css: ['<?= url("css/style.css") ?>'],
    content_style: `
      body { max-width: 760px; margin: 1rem auto; line-height: 1.7; }
      figure { margin: 1.2rem 0; }
      figcaption { font-size: .9rem; opacity: .8; text-align: center; }
      img { border-radius: 6px; }
      blockquote { border-left: 4px solid var(--theme-primary, #8a4); padding:.6rem 1rem; background:rgba(0,0,0,.03); }
      table { border-collapse: collapse; width: 100%; }
      table, th, td { border: 1px solid #ddd; }
      th, td { padding: .5rem; }
      ul, ol { margin-left: 1.2rem; }
      pre, code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace; }
    `,

    browser_spellcheck: true,
    contextmenu: false
  });
}
</script>

<!-- Auto-slug -->
<script<?= $nonceAttr ?>>
(function () {
  const t = document.getElementById('titulo');
  const s = document.getElementById('slug');
  let userEditedSlug = false;

  s.addEventListener('input', () => { userEditedSlug = true; });

  function slugify(str) {
    return str
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '')
      .substring(0, 191);
  }

  // Solo autogenera si el slug está vacío (en edición normalmente no lo estará)
  t.addEventListener('input', () => {
    if (!userEditedSlug && !s.value) {
      s.value = slugify(t.value);
    }
  });
})();
</script>

<!-- Scripts para gestión de posts relacionados -->
<script<?= $nonceAttr ?>>
(function() {
    'use strict';

    const CSRF_TOKEN = '<?= $security->csrfToken() ?>';
    const ID_PAGINA = <?= $pagina_id ?>;

    // Elementos del DOM
    const modal = document.getElementById('modalAddPost');
    const btnAddPost = document.getElementById('btnAddPost');
    const modalClose = modal?.querySelector('.modal-close');
    const searchInput = document.getElementById('searchPosts');
    const filterCategoria = document.getElementById('filterCategoria');
    const searchResults = document.getElementById('searchResults');
    const btnAddSelected = document.getElementById('btnAddSelected');
    const tipoVisualizacion = document.getElementById('tipoVisualizacion');
    const postsList = document.getElementById('postsRelacionadosList');

    let searchTimeout = null;
    let selectedPosts = new Set();

    // ========================================
    // MODAL
    // ========================================

    function openModal() {
        modal.classList.add('active');
        selectedPosts.clear();
        searchPosts('');
    }

    function closeModal() {
        modal.classList.remove('active');
        searchInput.value = '';
        filterCategoria.value = '';
        selectedPosts.clear();
    }

    btnAddPost?.addEventListener('click', openModal);
    modalClose?.addEventListener('click', closeModal);

    // Cerrar al hacer click fuera del modal
    modal?.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // ========================================
    // BÚSQUEDA DE POSTS
    // ========================================

    function searchPosts(query) {
        const categoria = filterCategoria.value;
        const url = new URL('<?= url("api/post_search.php") ?>', window.location.origin);
        url.searchParams.set('q', query);
        url.searchParams.set('id_pagina', ID_PAGINA);
        if (categoria) url.searchParams.set('categoria', categoria);

        searchResults.innerHTML = '<p class="loading">Buscando...</p>';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    searchResults.innerHTML = '<p class="empty">Error al buscar posts</p>';
                    return;
                }

                if (data.posts.length === 0) {
                    searchResults.innerHTML = '<p class="empty">No se encontraron posts</p>';
                    return;
                }

                renderSearchResults(data.posts);
            })
            .catch(err => {
                console.error('Error:', err);
                searchResults.innerHTML = '<p class="empty">Error de conexión</p>';
            });
    }

    function renderSearchResults(posts) {
        searchResults.innerHTML = '';

        posts.forEach(post => {
            const item = document.createElement('div');
            item.className = 'post-search-item';
            if (post.en_pagina) item.classList.add('disabled');

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = post.id_post;
            checkbox.id = `post-${post.id_post}`;
            checkbox.disabled = post.en_pagina;

            checkbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    selectedPosts.add(parseInt(post.id_post));
                } else {
                    selectedPosts.delete(parseInt(post.id_post));
                }
            });

            const label = document.createElement('label');
            label.htmlFor = `post-${post.id_post}`;

            let badgeHtml = '';
            if (post.en_pagina) {
                badgeHtml = '<span class="post-badge warning">Ya está en esta página</span>';
            } else if (post.paginas && post.paginas.length > 0) {
                badgeHtml = `<span class="post-badge">Aparece en: ${post.paginas.join(', ')}</span>`;
            }

            label.innerHTML = `
                <strong>${escapeHtml(post.titulo)}</strong>
                <small>
                    Categoría: ${escapeHtml(post.nombre_categoria || 'Sin categoría')} |
                    ${formatDate(post.fecha_publicacion)}
                    ${badgeHtml}
                </small>
            `;

            item.appendChild(checkbox);
            item.appendChild(label);
            searchResults.appendChild(item);
        });
    }

    // Búsqueda con debounce
    searchInput?.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchPosts(e.target.value);
        }, 300);
    });

    filterCategoria?.addEventListener('change', () => {
        searchPosts(searchInput.value);
    });

    // ========================================
    // AÑADIR POSTS SELECCIONADOS
    // ========================================

    btnAddSelected?.addEventListener('click', async () => {
        if (selectedPosts.size === 0) {
            alert('Selecciona al menos un post');
            return;
        }

        const tipo = tipoVisualizacion.value;
        btnAddSelected.disabled = true;
        btnAddSelected.textContent = 'Añadiendo...';

        try {
            // Obtener el orden máximo actual
            const items = postsList.querySelectorAll('.post-item');
            let maxOrden = 0;
            items.forEach(item => {
                const orden = parseInt(item.dataset.orden) || 0;
                if (orden > maxOrden) maxOrden = orden;
            });

            // Añadir cada post
            const promises = Array.from(selectedPosts).map((idPost, index) => {
                const formData = new FormData();
                formData.append('csrf_token', CSRF_TOKEN);
                formData.append('id_pagina', ID_PAGINA);
                formData.append('id_post', idPost);
                formData.append('orden', maxOrden + (index + 1) * 10);
                formData.append('tipo', tipo);

                return fetch('<?= url("api/pagina_posts_add.php") ?>', {
                    method: 'POST',
                    body: formData
                }).then(res => res.json());
            });

            const results = await Promise.all(promises);
            const allSuccess = results.every(r => r.success);

            if (allSuccess) {
                // Recargar página para mostrar los nuevos posts
                window.location.reload();
            } else {
                alert('Algunos posts no se pudieron añadir');
                btnAddSelected.disabled = false;
                btnAddSelected.textContent = 'Añadir Seleccionados';
            }

        } catch (err) {
            console.error('Error:', err);
            alert('Error al añadir posts');
            btnAddSelected.disabled = false;
            btnAddSelected.textContent = 'Añadir Seleccionados';
        }
    });

    // ========================================
    // ELIMINAR POST
    // ========================================

    postsList?.addEventListener('click', async (e) => {
        if (e.target.classList.contains('btn-remove')) {
            const idPost = e.target.dataset.id;

            if (!confirm('¿Eliminar este post de la página?')) return;

            const formData = new FormData();
            formData.append('csrf_token', CSRF_TOKEN);
            formData.append('id_pagina', ID_PAGINA);
            formData.append('id_post', idPost);

            try {
                const res = await fetch('<?= url("api/pagina_posts_remove.php") ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    // Eliminar del DOM
                    const item = e.target.closest('.post-item');
                    item.remove();

                    // Si no quedan posts, mostrar mensaje vacío
                    if (postsList.querySelectorAll('.post-item').length === 0) {
                        postsList.innerHTML = '<p class="empty-state" style="padding: 2rem; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">No hay posts relacionados.</p>';
                    }

                    // Actualizar contador
                    const counter = document.querySelector('.posts-relacionados-manager h2');
                    if (counter) {
                        const count = postsList.querySelectorAll('.post-item').length;
                        counter.textContent = `📚 Posts Relacionados (${count})`;
                    }
                } else {
                    alert('Error al eliminar: ' + (data.error || 'Desconocido'));
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error de conexión');
            }
        }
    });

    // ========================================
    // CAMBIAR TIPO DE VISUALIZACIÓN
    // ========================================

    postsList?.addEventListener('change', async (e) => {
        if (e.target.classList.contains('tipo-viz')) {
            const idPost = e.target.dataset.id;
            const tipo = e.target.value;

            const formData = new FormData();
            formData.append('csrf_token', CSRF_TOKEN);
            formData.append('id_pagina', ID_PAGINA);
            formData.append('id_post', idPost);
            formData.append('tipo', tipo);

            try {
                const res = await fetch('<?= url("api/pagina_posts_add.php") ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();

                if (!data.success) {
                    alert('Error al cambiar tipo: ' + (data.error || 'Desconocido'));
                    // Revertir el cambio
                    e.target.value = tipo === 'card' ? 'embebido' : 'card';
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error de conexión');
            }
        }
    });

    // ========================================
    // DRAG & DROP PARA REORDENAR
    // ========================================

    let draggedItem = null;

    postsList?.addEventListener('dragstart', (e) => {
        if (e.target.classList.contains('post-item')) {
            draggedItem = e.target;
            e.target.classList.add('dragging');
        }
    });

    postsList?.addEventListener('dragend', (e) => {
        if (e.target.classList.contains('post-item')) {
            e.target.classList.remove('dragging');
        }
    });

    postsList?.addEventListener('dragover', (e) => {
        e.preventDefault();
        const afterElement = getDragAfterElement(postsList, e.clientY);
        if (afterElement == null) {
            postsList.appendChild(draggedItem);
        } else {
            postsList.insertBefore(draggedItem, afterElement);
        }
    });

    postsList?.addEventListener('drop', async (e) => {
        e.preventDefault();
        await saveNewOrder();
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.post-item:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    async function saveNewOrder() {
        const items = postsList.querySelectorAll('.post-item');
        const ordenNuevo = [];

        items.forEach((item, index) => {
            ordenNuevo.push({
                id_post: parseInt(item.dataset.id),
                orden: (index + 1) * 10
            });
            item.dataset.orden = (index + 1) * 10;
        });

        const formData = new FormData();
        formData.append('csrf_token', CSRF_TOKEN);
        formData.append('id_pagina', ID_PAGINA);
        formData.append('orden', JSON.stringify(ordenNuevo));

        try {
            const res = await fetch('<?= url("api/pagina_posts_reorder.php") ?>', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (!data.success) {
                console.error('Error al reordenar:', data.error);
            }
        } catch (err) {
            console.error('Error:', err);
        }
    }

    // Hacer los items arrastrables
    postsList?.querySelectorAll('.post-item').forEach(item => {
        item.setAttribute('draggable', 'true');
    });

    // ========================================
    // UTILIDADES
    // ========================================

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

})();
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
