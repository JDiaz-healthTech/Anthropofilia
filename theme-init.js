// === INICIO SNIPPET theme-init.js (reemplazar entero) ===
(function(){
  'use strict';
  var root = document.documentElement;

  // Leer preferencias persistidas
  var theme = localStorage.getItem('theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  var hc    = localStorage.getItem('a11y_hc') === '1';

  // Aplicar clases sin FOUC (independientes)
  root.classList.toggle('theme-dark', theme === 'dark');
  // Si quisieras una clase 'theme-light' podrías activarla aquí, pero no es necesaria si no la usas en CSS
  root.classList.toggle('high-contrast', hc);

  // Font zoom (si existiese de sesiones previas)
  var rawZoom = parseInt(localStorage.getItem('a11y_zoom') || '100', 10);
  if (rawZoom && isFinite(rawZoom)) {
    root.style.setProperty('--font-zoom', String(rawZoom/100));
  }

  // Valores opcionales por data-* (no afectan HC)
  if (!hc){
    var primaryColor = root.dataset.primaryColor || '';
    var pageBg       = root.dataset.bgColor || '';
    var headerBg     = root.dataset.headerBg || '';
    if (primaryColor) root.style.setProperty('--brand', primaryColor);
    if (pageBg)       root.style.setProperty('--page-bg', pageBg);
    if (headerBg){
      var safeUrl = headerBg.replace(/"/g, '\\"').replace(/\s+/g, '%20');
      var header  = document.querySelector('.main-header');
      if (header){
        header.style.setProperty('--header-image', 'url("'+safeUrl+'")');
        header.classList.add('has-bg-image');
      }
    }
  }
})();
// === FIN SNIPPET theme-init.js ===
