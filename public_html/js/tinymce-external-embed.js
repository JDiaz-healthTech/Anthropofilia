/**
 * TinyMCE Plugin: Insertar Contenido Externo
 *
 * Campo único inteligente que detecta automáticamente la plataforma
 * (Genially, Calameo, YouTube, Vimeo, Google Drive, PDF)
 * y genera el iframe/enlace correspondiente.
 *
 * Ubicación: public_html/js/tinymce-external-embed.js
 */
(function () {
    'use strict';

    /**
     * Proveedores soportados.
     * Cada uno define: nombre, icono, regex de detección y función
     * que transforma la URL/código en HTML insertable.
     */
    var providers = [
        {
            name: 'Genially',
            icon: '🎮',
            // Acepta: https://view.genially.com/XXXXX...
            regex: /^https?:\/\/view\.genially\.com\/([a-z0-9]+)/i,
            transform: function (url, match) {
                return {
                    html:
                        '<div contenteditable="false" style="width:100%;position:relative;padding-bottom:56.25%;height:0;">' +
                        '<iframe src="' +
                        escapeAttr(url) +
                        '" ' +
                        'style="position:absolute;top:0;left:0;width:100%;height:100%;" ' +
                        'frameborder="0" scrolling="yes" allowfullscreen="allowfullscreen" ' +
                        'title="Genially"></iframe></div>',
                    type: 'embed',
                };
            },
        },
        {
            name: 'Calameo',
            icon: '📖',
            // Acepta:
            //   https://www.calameo.com/read/XXXXX
            //   https://www.calameo.com/books/XXXXX
            //   https://v.calameo.com/?bkcode=XXXXX
            regex: /^https?:\/\/(?:www\.)?calameo\.com\/(?:read|books)\/([a-z0-9]+)/i,
            transform: function (url, match) {
                var bookCode = match[1];
                return {
                    html:
                        '<div contenteditable="false" style="text-align:center;margin:1.5rem 0;">' +
                        '<iframe src="//v.calameo.com/?bkcode=' +
                        escapeAttr(bookCode) +
                        '&mode=mini" ' +
                        'width="480" height="300" frameborder="0" scrolling="no" ' +
                        'allowfullscreen="allowfullscreen" title="Calameo"></iframe></div>',
                    type: 'embed',
                };
            },
        },
        {
            name: 'YouTube',
            icon: '▶️',
            regex: /^https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/i,
            transform: function (url, match) {
                var videoId = match[1];
                return {
                    html:
                        '<iframe width="560" height="315" ' +
                        'src="https://www.youtube.com/embed/' +
                        escapeAttr(videoId) +
                        '" ' +
                        'frameborder="0" allow="accelerometer; autoplay; clipboard-write; ' +
                        'encrypted-media; gyroscope; picture-in-picture" ' +
                        'allowfullscreen="allowfullscreen" title="YouTube"></iframe>',
                    type: 'embed',
                };
            },
        },
        {
            name: 'Vimeo',
            icon: '🎬',
            regex: /^https?:\/\/(?:www\.)?vimeo\.com\/(\d+)/i,
            transform: function (url, match) {
                var videoId = match[1];
                return {
                    html:
                        '<iframe src="https://player.vimeo.com/video/' +
                        escapeAttr(videoId) +
                        '" ' +
                        'width="560" height="315" frameborder="0" ' +
                        'allow="autoplay; fullscreen; picture-in-picture" ' +
                        'allowfullscreen="allowfullscreen" title="Vimeo"></iframe>',
                    type: 'embed',
                };
            },
        },
        {
            name: 'Google Drive',
            icon: '📁',
            // Acepta: https://drive.google.com/file/d/XXXXX/view
            //         https://docs.google.com/presentation/d/XXXXX/...
            //         https://docs.google.com/document/d/XXXXX/...
            regex: /^https?:\/\/(?:drive|docs)\.google\.com\/(?:file\/d\/|presentation\/d\/|document\/d\/|spreadsheets\/d\/)([a-zA-Z0-9_-]+)/i,
            transform: function (url, match) {
                var fileId = match[1];
                // Detectar tipo de documento de Google
                if (url.indexOf('presentation') !== -1) {
                    return {
                        html:
                            '<iframe src="https://docs.google.com/presentation/d/' +
                            escapeAttr(fileId) +
                            '/embed?start=false&loop=false" ' +
                            'width="560" height="315" frameborder="0" ' +
                            'allowfullscreen="allowfullscreen" title="Google Slides"></iframe>',
                        type: 'embed',
                    };
                }
                if (url.indexOf('document') !== -1) {
                    return {
                        html:
                            '<iframe src="https://docs.google.com/document/d/' +
                            escapeAttr(fileId) +
                            '/pub?embedded=true" ' +
                            'width="100%" height="500" frameborder="0" ' +
                            'title="Google Docs"></iframe>',
                        type: 'embed',
                    };
                }
                if (url.indexOf('spreadsheets') !== -1) {
                    return {
                        html:
                            '<iframe src="https://docs.google.com/spreadsheets/d/' +
                            escapeAttr(fileId) +
                            '/pubhtml?widget=true" ' +
                            'width="100%" height="400" frameborder="0" ' +
                            'title="Google Sheets"></iframe>',
                        type: 'embed',
                    };
                }
                // Drive genérico (PDF u otro archivo) → preview
                return {
                    html:
                        '<iframe src="https://drive.google.com/file/d/' +
                        escapeAttr(fileId) +
                        '/preview" ' +
                        'width="560" height="400" frameborder="0" ' +
                        'allowfullscreen="allowfullscreen" title="Google Drive"></iframe>',
                    type: 'embed',
                };
            },
        },
        {
            name: 'PDF',
            icon: '📄',
            // Cualquier URL que termine en .pdf (opcionalmente con query params)
            regex: /^https?:\/\/.+\.pdf(\?.*)?$/i,
            transform: function (url) {
                // Retorna null para indicar que necesita elegir modo
                return {
                    html: null,
                    type: 'pdf',
                    url: url,
                };
            },
        },
    ];

    /**
     * Detectar iframe pegado directamente (caso genérico/avanzado)
     */
    function detectRawIframe(input) {
        var trimmed = input.trim();
        var match = trimmed.match(/<iframe\s[^>]*src=["']([^"']+)["'][^>]*>[\s\S]*?<\/iframe>/i);
        if (match) {
            // Extraer solo el iframe limpio, envolverlo en div no editable
            var iframeTag = match[0];
            return {
                name: 'Código de inserción',
                icon: '📋',
                html: '<div contenteditable="false" style="margin:1.5rem 0;">' + iframeTag + '</div>',
                type: 'iframe',
            };
        }
        return null;
    }

    /**
     * Detectar qué proveedor corresponde al input del usuario
     */
    function detectProvider(input) {
        var trimmed = input.trim();
        if (!trimmed) return null;

        // 1. Probar cada proveedor por URL
        for (var i = 0; i < providers.length; i++) {
            var match = trimmed.match(providers[i].regex);
            if (match) {
                var result = providers[i].transform(trimmed, match);
                return {
                    name: providers[i].name,
                    icon: providers[i].icon,
                    html: result.html,
                    type: result.type,
                    url: result.url || trimmed,
                };
            }
        }

        // 2. Probar si es un iframe pegado directamente
        var iframeResult = detectRawIframe(trimmed);
        if (iframeResult) return iframeResult;

        // 3. No reconocido
        return null;
    }

    /**
     * Generar HTML para un PDF según el modo elegido
     */
    function generatePdfHtml(url, mode) {
        if (mode === 'embed') {
            return (
                '<iframe src="' +
                escapeAttr(url) +
                '" ' +
                'width="100%" height="500" frameborder="0" ' +
                'title="Documento PDF"></iframe>'
            );
        }
        // mode === 'link'
        var fileName = url.split('/').pop().split('?')[0] || 'documento.pdf';
        return (
            '<p><a href="' +
            escapeAttr(url) +
            '" target="_blank" rel="noopener" ' +
            'class="pdf-link">📄 ' +
            escapeHtml(decodeURIComponent(fileName)) +
            '</a></p>'
        );
    }

    /**
     * Escapar atributos HTML
     */
    function escapeAttr(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    /**
     * Escapar contenido HTML
     */
    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    // ========================================
    // REGISTRO DEL PLUGIN
    // ========================================

    tinymce.PluginManager.add('externalembed', function (editor) {
        var modal = null;
        var currentDetection = null;

        /**
         * Crear el modal
         */
        function createModal() {
            var overlay = document.createElement('div');
            overlay.className = 'ext-embed-overlay';
            overlay.innerHTML =
                '<div class="ext-embed-modal">' +
                '<div class="ext-embed-modal__header">' +
                '<h3>Insertar contenido externo</h3>' +
                '<button type="button" class="ext-embed-modal__close" title="Cerrar">&times;</button>' +
                '</div>' +
                '<div class="ext-embed-modal__body">' +
                '<label class="ext-embed-modal__label" for="extEmbedInput">' +
                'Pega aquí el enlace o código de inserción:' +
                '</label>' +
                '<textarea id="extEmbedInput" class="ext-embed-modal__input" rows="3" ' +
                'placeholder="Ejemplo: https://view.genially.com/61a101bf... o el código de inserción que te proporcione la plataforma"></textarea>' +
                '<div class="ext-embed-modal__platforms">' +
                '<span title="Genially">🎮 Genially</span>' +
                '<span title="Calameo">📖 Calameo</span>' +
                '<span title="YouTube">▶️ YouTube</span>' +
                '<span title="Vimeo">🎬 Vimeo</span>' +
                '<span title="Google Drive">📁 Google Drive</span>' +
                '<span title="PDF">📄 PDF</span>' +
                '<span title="Código de inserción">📋 Código</span>' +
                '</div>' +
                '<div class="ext-embed-modal__feedback" aria-live="polite"></div>' +
                '<div class="ext-embed-modal__pdf-options" style="display:none;">' +
                '<p>¿Cómo quieres mostrar el PDF?</p>' +
                '<label class="ext-embed-modal__radio">' +
                '<input type="radio" name="pdfMode" value="embed" checked> ' +
                'Embebido en la página (visor integrado)' +
                '</label>' +
                '<label class="ext-embed-modal__radio">' +
                '<input type="radio" name="pdfMode" value="link"> ' +
                'Enlace con icono de PDF' +
                '</label>' +
                '</div>' +
                '</div>' +
                '<div class="ext-embed-modal__footer">' +
                '<button type="button" class="ext-embed-modal__btn ext-embed-modal__btn--cancel">' +
                'Cancelar' +
                '</button>' +
                '<button type="button" class="ext-embed-modal__btn ext-embed-modal__btn--insert" disabled>' +
                'Insertar' +
                '</button>' +
                '</div>' +
                '</div>';

            document.body.appendChild(overlay);
            return overlay;
        }

        /**
         * Actualizar el feedback visual según lo que se detecte
         */
        function updateFeedback(input) {
            var feedbackEl = modal.querySelector('.ext-embed-modal__feedback');
            var pdfOptions = modal.querySelector('.ext-embed-modal__pdf-options');
            var insertBtn = modal.querySelector('.ext-embed-modal__btn--insert');

            var trimmed = input.trim();

            // Vacío → estado inicial
            if (!trimmed) {
                feedbackEl.innerHTML = '';
                feedbackEl.className = 'ext-embed-modal__feedback';
                pdfOptions.style.display = 'none';
                insertBtn.disabled = true;
                currentDetection = null;
                return;
            }

            // Detectar
            var detection = detectProvider(trimmed);
            currentDetection = detection;

            if (detection) {
                if (detection.type === 'pdf') {
                    // Mostrar opciones de PDF
                    feedbackEl.innerHTML = detection.icon + ' <strong>PDF detectado</strong>';
                    feedbackEl.className = 'ext-embed-modal__feedback ext-embed-modal__feedback--success';
                    pdfOptions.style.display = 'block';
                    insertBtn.disabled = false;
                } else {
                    // Proveedor reconocido
                    feedbackEl.innerHTML =
                        detection.icon +
                        ' <strong>' +
                        escapeHtml(detection.name) +
                        ' detectado</strong> — listo para insertar';
                    feedbackEl.className = 'ext-embed-modal__feedback ext-embed-modal__feedback--success';
                    pdfOptions.style.display = 'none';
                    insertBtn.disabled = false;
                }
            } else {
                // No reconocido
                feedbackEl.innerHTML =
                    '⚠️ No reconozco este tipo de contenido. ' +
                    'Asegúrate de pegar un enlace válido o contacta con el administrador.';
                feedbackEl.className = 'ext-embed-modal__feedback ext-embed-modal__feedback--error';
                pdfOptions.style.display = 'none';
                insertBtn.disabled = true;
                currentDetection = null;
            }
        }

        /**
         * Insertar el contenido detectado en el editor
         */
        function insertContent() {
            if (!currentDetection) return;

            var html = '';

            if (currentDetection.type === 'pdf') {
                var mode = modal.querySelector('input[name="pdfMode"]:checked').value;
                html = generatePdfHtml(currentDetection.url, mode);
            } else if (currentDetection.type === 'iframe') {
                // Código iframe genérico pegado directamente
                html = currentDetection.html;
            } else {
                html = currentDetection.html;
            }

            if (html) {
                editor.insertContent(html + '<p>&nbsp;</p>');
            }

            closeModal();
        }

        /**
         * Abrir el modal
         */
        function openModal() {
            if (!modal) {
                modal = createModal();

                // Eventos
                modal.querySelector('.ext-embed-modal__close').addEventListener('click', closeModal);
                modal.querySelector('.ext-embed-modal__btn--cancel').addEventListener('click', closeModal);
                modal.querySelector('.ext-embed-modal__btn--insert').addEventListener('click', insertContent);

                // Detección en tiempo real con debounce
                var debounceTimer = null;
                var inputEl = modal.querySelector('#extEmbedInput');
                inputEl.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        updateFeedback(inputEl.value);
                    }, 400);
                });

                // También detectar al pegar (inmediato, sin debounce)
                inputEl.addEventListener('paste', function () {
                    setTimeout(function () {
                        updateFeedback(inputEl.value);
                    }, 50);
                });

                // Cerrar con clic fuera
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) closeModal();
                });

                // Cerrar con Escape
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
                        closeModal();
                    }
                });
            }

            // Reset
            currentDetection = null;
            modal.querySelector('#extEmbedInput').value = '';
            modal.querySelector('.ext-embed-modal__feedback').innerHTML = '';
            modal.querySelector('.ext-embed-modal__feedback').className = 'ext-embed-modal__feedback';
            modal.querySelector('.ext-embed-modal__pdf-options').style.display = 'none';
            modal.querySelector('.ext-embed-modal__btn--insert').disabled = true;

            // Mostrar
            modal.classList.add('active');
            modal.querySelector('#extEmbedInput').focus();
        }

        /**
         * Cerrar el modal
         */
        function closeModal() {
            if (modal) {
                modal.classList.remove('active');
            }
            currentDetection = null;
        }

        // Registrar botón en la toolbar
        editor.ui.registry.addButton('externalembed', {
            text: '🔗 Insertar contenido',
            tooltip: 'Insertar contenido externo (Genially, Calameo, YouTube, PDF...)',
            onAction: openModal,
        });
    });
})();
