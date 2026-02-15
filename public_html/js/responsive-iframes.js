/**
 * responsive-iframes.js
 *
 * Envuelve automáticamente los iframes del contenido en un wrapper
 * responsive que mantiene la proporción del vídeo/embed.
 * Aplica solo a iframes dentro de .contenido y .post-embebido__contenido
 */
(function () {
    'use strict';

    function init() {
        var containers = document.querySelectorAll('.contenido, .post-embebido__contenido');

        containers.forEach(function (container) {
            var iframes = container.querySelectorAll('iframe');

            iframes.forEach(function (iframe) {
                // Si ya está envuelto, saltar
                if (iframe.parentElement.classList.contains('iframe-responsive')) {
                    return;
                }

                // Calcular proporción del iframe original
                var w = parseInt(iframe.getAttribute('width'), 10) || 560;
                var h = parseInt(iframe.getAttribute('height'), 10) || 315;
                var ratio = (h / w) * 100;

                // Crear wrapper
                var wrapper = document.createElement('div');
                wrapper.className = 'iframe-responsive';
                wrapper.style.paddingBottom = ratio + '%';

                // Insertar wrapper y mover iframe dentro
                iframe.parentNode.insertBefore(wrapper, iframe);
                wrapper.appendChild(iframe);

                // Limpiar atributos fijos del iframe
                iframe.removeAttribute('width');
                iframe.removeAttribute('height');
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
