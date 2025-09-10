// theme-init.js — inicialización robusta y coherente con accessibility.js
(function(){
  'use strict';
  const root = document.documentElement;
  const primaryColor = root.dataset.primaryColor || '#0645ad';
  const bgColor = root.dataset.bgColor || '#ffffff';
  const headerBg = root.dataset.headerBg || '';

  // leer valores guardados (aceptamos '1' y 'true' por compatibilidad)
  const rawHc = localStorage.getItem('a11y_hc');
  const userHc  = rawHc === '1' || rawHc === 'true';
  const rawDark = localStorage.getItem('a11y_dark');
  const userDark = rawDark === '1' || rawDark === 'true';

  // Si el usuario pidió alto contraste, no pisamos variables críticas de HC.
  if (!userHc) {
    root.style.setProperty('--brand', primaryColor);
    root.style.setProperty('--page-bg', bgColor);
    if (headerBg) root.style.setProperty('--theme-header-bg', headerBg);
  } else {
    if (['localhost','127.0.0.1'].includes(window.location.hostname))
      console.log('theme-init: HC activo, se omiten overrides de variables');
  }

  // Aseguramos que solo exista una clase de tono (light OR dark)
  if (userDark) {
    root.classList.add('theme-dark');
    root.classList.remove('theme-light');
  } else {
    root.classList.remove('theme-dark');
    root.classList.add('theme-light');
  }
})();

// Añade al final de theme-init.js (o en un archivo cargado con defer)
document.addEventListener('DOMContentLoaded', function () {
  const html = document.documentElement;
  const headerBg = (html.dataset.headerBg || '').trim();
  if (!headerBg) return;

  const header = document.querySelector('.main-header');
  if (!header) return;

  // Sanitiza la URL para incluirla en CSS url("...")
  // Evita rupturas si la URL contiene comillas
  const safeUrl = headerBg.replace(/"/g, '\\"').replace(/\s+/g, '%20');

  // Aplicar como variable en el propio elemento (no en :root)
  header.style.setProperty('--header-image', `url("${safeUrl}")`);

  // Asegura que el CSS que depende de la clase se active
  header.classList.add('has-bg-image');
});