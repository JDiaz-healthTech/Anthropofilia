/**
 * ui.js - Sistema de preferencias de usuario
 * 
 * Expone window.Prefs como API unificada para:
 * - Tema oscuro/claro
 * - Alto contraste
 * - Zoom de fuente
 * 
 * @author Anthropofilia
 * @version 1.0.0
 */
(function() {
    'use strict';

    // =========================================================================
    // CONFIGURACIÓN
    // =========================================================================
    var STORAGE_KEYS = {
        theme: 'theme',
        hc: 'a11y_hc',
        zoom: 'a11y_zoom'
    };

    var DEFAULTS = {
        dark: false,
        hc: false,
        zoom: 100
    };

    var ZOOM_LIMITS = {
        min: 85,
        max: 160,
        step: 5
    };

    // =========================================================================
    // ESTADO INTERNO
    // =========================================================================
    var state = {
        dark: false,
        hc: false,
        zoom: 100
    };

    // =========================================================================
    // HELPERS
    // =========================================================================
    function loadFromStorage() {
        try {
            var savedTheme = localStorage.getItem(STORAGE_KEYS.theme);
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            state.dark = savedTheme 
                ? savedTheme === 'dark' 
                : prefersDark;
            
            state.hc = localStorage.getItem(STORAGE_KEYS.hc) === '1';
            
            var savedZoom = parseInt(localStorage.getItem(STORAGE_KEYS.zoom) || '100', 10);
            state.zoom = isFinite(savedZoom) ? savedZoom : DEFAULTS.zoom;
        } catch (e) {
            console.warn('[Prefs] No se pudo leer localStorage:', e);
        }
    }

    function saveToStorage() {
        try {
            localStorage.setItem(STORAGE_KEYS.theme, state.dark ? 'dark' : 'light');
            localStorage.setItem(STORAGE_KEYS.hc, state.hc ? '1' : '0');
            localStorage.setItem(STORAGE_KEYS.zoom, String(state.zoom));
        } catch (e) {
            console.warn('[Prefs] No se pudo guardar en localStorage:', e);
        }
    }

    function applyToDOM() {
        var root = document.documentElement;

        // Tema oscuro/claro
        root.classList.toggle('theme-dark', state.dark);
        root.classList.toggle('theme-light', !state.dark);

        // Alto contraste
        root.classList.toggle('high-contrast', state.hc);

        // Zoom de fuente
        root.style.setProperty('--font-zoom', String(state.zoom / 100));

        // Actualizar aria-pressed en botones (si existen)
        updateButtonStates();
    }

    function updateButtonStates() {
        var btnDark = document.getElementById('toggle-dark');
        var btnHC = document.getElementById('toggle-high-contrast');

        if (btnDark) {
            btnDark.setAttribute('aria-pressed', String(state.dark));
        }
        if (btnHC) {
            btnHC.setAttribute('aria-pressed', String(state.hc));
        }
    }

    // =========================================================================
    // API PÚBLICA
    // =========================================================================
    var Prefs = {
        /**
         * Obtiene el estado actual de preferencias
         * @returns {{dark: boolean, hc: boolean, zoom: number}}
         */
        get: function() {
            return {
                dark: state.dark,
                hc: state.hc,
                zoom: state.zoom
            };
        },

        /**
         * Actualiza preferencias
         * @param {{dark?: boolean, hc?: boolean, zoom?: number}} newValues
         */
        set: function(newValues) {
            if (typeof newValues !== 'object') return;

            if (typeof newValues.dark === 'boolean') {
                state.dark = newValues.dark;
            }
            if (typeof newValues.hc === 'boolean') {
                state.hc = newValues.hc;
            }
            if (typeof newValues.zoom === 'number' && isFinite(newValues.zoom)) {
                state.zoom = Math.max(ZOOM_LIMITS.min, Math.min(ZOOM_LIMITS.max, newValues.zoom));
            }

            saveToStorage();
            applyToDOM();
        },

        /**
         * Alterna tema oscuro/claro
         */
        toggleDark: function() {
            this.set({ dark: !state.dark });
        },

        /**
         * Alterna alto contraste
         */
        toggleHighContrast: function() {
            this.set({ hc: !state.hc });
        },

        /**
         * Aumenta zoom
         */
        zoomIn: function() {
            this.set({ zoom: state.zoom + ZOOM_LIMITS.step });
        },

        /**
         * Disminuye zoom
         */
        zoomOut: function() {
            this.set({ zoom: state.zoom - ZOOM_LIMITS.step });
        },

        /**
         * Resetea a valores por defecto
         */
        reset: function() {
            state.dark = DEFAULTS.dark;
            state.hc = DEFAULTS.hc;
            state.zoom = DEFAULTS.zoom;
            saveToStorage();
            applyToDOM();
        },

        // Constantes expuestas
        ZOOM_MIN: ZOOM_LIMITS.min,
        ZOOM_MAX: ZOOM_LIMITS.max,
        ZOOM_STEP: ZOOM_LIMITS.step
    };

    // =========================================================================
    // INICIALIZACIÓN
    // =========================================================================
    loadFromStorage();
    
    // Aplicar al DOM cuando esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyToDOM);
    } else {
        applyToDOM();
    }

    // Escuchar cambios en preferencias del sistema
    try {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            // Solo aplicar si el usuario no ha elegido manualmente
            if (!localStorage.getItem(STORAGE_KEYS.theme)) {
                Prefs.set({ dark: e.matches });
            }
        });
    } catch (e) {
        // Navegadores antiguos no soportan addEventListener en matchMedia
    }

    // Exponer globalmente
    window.Prefs = Prefs;

})();