/**
 * accessibility.js - Conecta controles de accesibilidad con Prefs API
 * 
 * Requiere: ui.js (window.Prefs)
 * 
 * Botones esperados:
 * - #toggle-dark          → Tema oscuro/claro
 * - #toggle-high-contrast → Alto contraste
 * - #increase-font-size   → Aumentar fuente
 * - #decrease-font-size   → Disminuir fuente
 * 
 * @author Anthropofilia
 * @version 1.0.0
 */
(function() {
    'use strict';

    function init() {
        // Verificar que Prefs esté disponible
        if (typeof window.Prefs === 'undefined') {
            console.warn('[Accessibility] Prefs API no disponible. ¿Se cargó ui.js?');
            return;
        }

        // Referencias a botones
        var buttons = {
            dark: document.getElementById('toggle-dark'),
            hc: document.getElementById('toggle-high-contrast'),
            zoomIn: document.getElementById('increase-font-size'),
            zoomOut: document.getElementById('decrease-font-size')
        };

        // Conectar eventos
        if (buttons.dark) {
            buttons.dark.addEventListener('click', function() {
                Prefs.toggleDark();
            });
        }

        if (buttons.hc) {
            buttons.hc.addEventListener('click', function() {
                Prefs.toggleHighContrast();
            });
        }

        if (buttons.zoomIn) {
            buttons.zoomIn.addEventListener('click', function() {
                Prefs.zoomIn();
            });
        }

        if (buttons.zoomOut) {
            buttons.zoomOut.addEventListener('click', function() {
                Prefs.zoomOut();
            });
        }

        // Atajos de teclado (opcional pero profesional)
        document.addEventListener('keydown', function(e) {
            // Solo si no está enfocado en un input/textarea
            if (e.target.matches('input, textarea, select')) return;
            
            // Alt + D = Toggle Dark
            if (e.altKey && e.key.toLowerCase() === 'd') {
                e.preventDefault();
                Prefs.toggleDark();
            }
            
            // Alt + H = Toggle High Contrast
            if (e.altKey && e.key.toLowerCase() === 'h') {
                e.preventDefault();
                Prefs.toggleHighContrast();
            }
            
            // Alt + Plus = Zoom In
            if (e.altKey && (e.key === '+' || e.key === '=')) {
                e.preventDefault();
                Prefs.zoomIn();
            }
            
            // Alt + Minus = Zoom Out
            if (e.altKey && e.key === '-') {
                e.preventDefault();
                Prefs.zoomOut();
            }
        });
    }

    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();