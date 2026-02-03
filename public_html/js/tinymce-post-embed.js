/**
 * TinyMCE Plugin: Insertar Post
 *
 * Añade un botón a la barra de herramientas que abre un modal
 * para buscar y seleccionar posts existentes e insertarlos como marcador.
 *
 * Ubicación: public_html/js/tinymce-post-embed.js
 */
(function () {
    'use strict';

    // Configuración (se sobrescribe desde PHP)
    window.PostEmbedConfig = window.PostEmbedConfig || {
        searchUrl: '/api/post_search.php',
        csrfToken: '',
    };

    /**
     * Registrar el plugin en TinyMCE
     */
    tinymce.PluginManager.add('postembed', function (editor) {
        // Estado del modal
        let modal = null;
        let selectedPost = null;
        let posts = [];
        let searchTimeout = null;

        /**
         * Crear el HTML del modal
         */
        function createModal() {
            const overlay = document.createElement('div');
            overlay.className = 'post-embed-modal-overlay';
            overlay.innerHTML = `
                <div class="post-embed-modal">
                    <div class="post-embed-modal__header">
                        <h3>📄 Insertar Post</h3>
                        <button type="button" class="post-embed-modal__close" title="Cerrar">&times;</button>
                    </div>
                    <div class="post-embed-modal__body">
                        <div class="post-embed-modal__search">
                            <input type="text"
                                   class="post-embed-modal__input"
                                   placeholder="Buscar posts por título..."
                                   autocomplete="off">
                        </div>
                        <div class="post-embed-modal__results">
                            <p class="post-embed-modal__loading">Cargando posts...</p>
                        </div>
                    </div>
                    <div class="post-embed-modal__footer">
                        <div class="post-embed-modal__options">
                            <label>
                                Mostrar como:
                                <select class="post-embed-modal__tipo">
                                    <option value="card">Card (preview)</option>
                                    <option value="embebido">Embebido (completo)</option>
                                </select>
                            </label>
                        </div>
                        <div class="post-embed-modal__actions">
                            <button type="button" class="post-embed-modal__btn post-embed-modal__btn--cancel">
                                Cancelar
                            </button>
                            <button type="button" class="post-embed-modal__btn post-embed-modal__btn--insert" disabled>
                                Insertar Post
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);
            return overlay;
        }

        /**
         * Buscar posts en el servidor
         */
        async function searchPosts(query = '') {
            const resultsContainer = modal.querySelector('.post-embed-modal__results');
            resultsContainer.innerHTML = '<p class="post-embed-modal__loading">Buscando...</p>';

            try {
                const url = new URL(window.PostEmbedConfig.searchUrl, window.location.origin);
                url.searchParams.set('q', query);

                const response = await fetch(url);
                const data = await response.json();

                if (!data.success) {
                    resultsContainer.innerHTML = '<p class="post-embed-modal__empty">Error al buscar posts</p>';
                    return;
                }

                posts = data.posts || [];

                if (posts.length === 0) {
                    resultsContainer.innerHTML = '<p class="post-embed-modal__empty">No se encontraron posts</p>';
                    return;
                }

                renderResults();
            } catch (err) {
                console.error('Error buscando posts:', err);
                resultsContainer.innerHTML = '<p class="post-embed-modal__empty">Error de conexión</p>';
            }
        }

        /**
         * Renderizar lista de resultados
         */
        function renderResults() {
            const resultsContainer = modal.querySelector('.post-embed-modal__results');
            resultsContainer.innerHTML = '';

            const list = document.createElement('ul');
            list.className = 'post-embed-modal__list';

            posts.forEach((post) => {
                const item = document.createElement('li');
                item.className = 'post-embed-modal__item';
                item.dataset.id = post.id_post;
                item.dataset.slug = post.slug;
                item.dataset.titulo = post.titulo;

                const fecha = new Date(post.fecha_publicacion);
                const fechaStr = fecha.toLocaleDateString('es-ES');

                item.innerHTML = `
                    <div class="post-embed-modal__item-content">
                        <strong>${escapeHtml(post.titulo)}</strong>
                        <small>
                            ${escapeHtml(post.nombre_categoria || 'Sin categoría')} · ${fechaStr}
                        </small>
                    </div>
                `;

                item.addEventListener('click', () => selectPost(item, post));
                list.appendChild(item);
            });

            resultsContainer.appendChild(list);
        }

        /**
         * Seleccionar un post
         */
        function selectPost(element, post) {
            // Quitar selección anterior
            modal.querySelectorAll('.post-embed-modal__item--selected').forEach((el) => {
                el.classList.remove('post-embed-modal__item--selected');
            });

            // Seleccionar nuevo
            element.classList.add('post-embed-modal__item--selected');
            selectedPost = post;

            // Habilitar botón de insertar
            modal.querySelector('.post-embed-modal__btn--insert').disabled = false;
        }

        /**
         * Insertar el marcador en el editor
         */
        function insertMarker() {
            if (!selectedPost) return;

            const tipo = modal.querySelector('.post-embed-modal__tipo').value;
            const marker = `[POST id="${selectedPost.id_post}" tipo="${tipo}"]`;

            // Insertar en la posición del cursor
            editor.insertContent(`<p>${marker}</p>`);

            closeModal();
        }

        /**
         * Abrir el modal
         */
        function openModal() {
            if (!modal) {
                modal = createModal();

                // Event listeners
                modal.querySelector('.post-embed-modal__close').addEventListener('click', closeModal);
                modal.querySelector('.post-embed-modal__btn--cancel').addEventListener('click', closeModal);
                modal.querySelector('.post-embed-modal__btn--insert').addEventListener('click', insertMarker);

                // Búsqueda con debounce
                const searchInput = modal.querySelector('.post-embed-modal__input');
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        searchPosts(e.target.value);
                    }, 300);
                });

                // Cerrar al hacer clic fuera
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal();
                });

                // Cerrar con Escape
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && modal.classList.contains('active')) {
                        closeModal();
                    }
                });
            }

            // Reset estado
            selectedPost = null;
            modal.querySelector('.post-embed-modal__input').value = '';
            modal.querySelector('.post-embed-modal__btn--insert').disabled = true;
            modal.querySelector('.post-embed-modal__tipo').value = 'card';

            // Mostrar
            modal.classList.add('active');
            modal.querySelector('.post-embed-modal__input').focus();

            // Cargar posts iniciales
            searchPosts('');
        }

        /**
         * Cerrar el modal
         */
        function closeModal() {
            if (modal) {
                modal.classList.remove('active');
            }
            selectedPost = null;
        }

        /**
         * Escapar HTML
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        // Registrar el botón en la barra de herramientas
        editor.ui.registry.addButton('postembed', {
            text: '📄 Post',
            tooltip: 'Insertar Post',
            onAction: openModal,
        });

        // También como item de menú
        editor.ui.registry.addMenuItem('postembed', {
            text: 'Insertar Post',
            icon: 'document-properties',
            onAction: openModal,
        });
    });
})();
