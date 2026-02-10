// toc.js - Funcionalidad del índice de contenidos
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.querySelector('.toc__toggle');
        const list = document.querySelector('.toc__list');

        if (!toggle || !list) return;

        toggle.addEventListener('click', function () {
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

            toggle.setAttribute('aria-expanded', !isExpanded);
            list.classList.toggle('collapsed', isExpanded);
        });

        // Scroll suave al hacer clic en enlaces del índice
        document.querySelectorAll('.toc__link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').slice(1);
                const target = document.getElementById(targetId);

                if (target) {
                    const offset = 80; // Espacio para header fijo si lo hay
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth',
                    });

                    // Actualizar URL sin recargar
                    history.pushState(null, '', '#' + targetId);
                }
            });
        });
    });
})();
