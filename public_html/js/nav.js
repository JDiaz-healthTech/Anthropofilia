(function () {
    'use strict';

    function init() {
        // === HAMBURGUESA ===
        var toggle = document.querySelector('.mobile-nav-toggle');
        var panel = document.getElementById('nav-mobile-panel');

        if (toggle && panel) {
            toggle.addEventListener('click', function () {
                var isOpen = panel.classList.toggle('active');
                this.classList.toggle('is-open', isOpen);
                this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                this.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
            });

            panel.addEventListener('click', function (e) {
                if (e.target.tagName === 'A') {
                    toggle.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');
                    panel.classList.remove('active');
                }
            });

            var sectionBtn = panel.querySelector('.nav-mobile-section-toggle');
            var sectionList = panel.querySelector('.nav-mobile-section-list');

            if (sectionBtn && sectionList) {
                sectionBtn.addEventListener('click', function () {
                    var expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', String(!expanded));
                    sectionList.classList.toggle('active', !expanded);
                });
            }
        }

        // === FAB ACCESIBILIDAD ===
        var fab = document.getElementById('a11y-fab-toggle');
        var fabPanel = document.getElementById('a11y-fab-panel');

        if (fab && fabPanel) {
            fab.addEventListener('click', function () {
                var isOpen = fabPanel.classList.toggle('active');
                this.setAttribute('aria-expanded', String(isOpen));
            });

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.a11y-fab-wrapper')) {
                    fabPanel.classList.remove('active');
                    fab.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && fabPanel.classList.contains('active')) {
                    fabPanel.classList.remove('active');
                    fab.setAttribute('aria-expanded', 'false');
                    fab.focus();
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
