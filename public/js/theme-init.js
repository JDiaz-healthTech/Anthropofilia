// theme-init.js - Sistema unificado de temas
(function(){
  'use strict';
  var root = document.documentElement;

  // =================================================================
  // 1. CARGAR PREFERENCIAS
  // =================================================================
  var savedTheme = localStorage.getItem('theme');
  var prefersDark = matchMedia('(prefers-color-scheme: dark)').matches;
  var theme = savedTheme || (prefersDark ? 'dark' : 'light');
  var hc = localStorage.getItem('a11y_hc') === '1';

  // =================================================================
  // 2. APLICAR CLASES (esto define TODO el tema via CSS)
  // =================================================================
  root.classList.toggle('theme-dark', theme === 'dark');
  root.classList.toggle('theme-light', theme === 'light');
  root.classList.toggle('high-contrast', hc);

  // =================================================================
  // 3. ZOOM DE FUENTE
  // =================================================================
  var zoom = parseInt(localStorage.getItem('a11y_zoom') || '100', 10);
  if (zoom && isFinite(zoom)) {
    root.style.setProperty('--font-zoom', String(zoom/100));
  }

  // =================================================================
  // 4. IMAGEN DE CABECERA (NO tocar colores)
  // =================================================================
  var headerBg = root.dataset.headerBg || '';
  if (headerBg) {
    var safeUrl = headerBg.replace(/"/g, '\\"').replace(/\s+/g, '%20');
    var header = document.querySelector('.main-header');
    if (header) {
      header.style.backgroundImage = 'url("' + safeUrl + '")';
      header.classList.add('has-bg-image');
    }
  }

  // =================================================================
  // NOTA: NO aplicamos data-primary-color ni data-bg-color
  // El tema se controla 100% desde variables.css
  // =================================================================
})();