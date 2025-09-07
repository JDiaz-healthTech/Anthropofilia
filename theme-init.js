// // theme-init.js - Inicialización del tema dinámico
// (function() {
//     'use strict';
    
//     // Obtener configuración desde data attributes del HTML
//     const html = document.documentElement;
//     const primaryColor = html.dataset.primaryColor || '#0645ad';
//     const bgColor = html.dataset.bgColor || '#ffffff';
//     const headerBg = html.dataset.headerBg || '';
    
//     // Aplicar variables CSS dinámicamente
//     function applyThemeColors() {
//         const root = document.documentElement;
//         root.style.setProperty('--brand', primaryColor);
//         root.style.setProperty('--page-bg', bgColor);
        
//         // Aplicar imagen de header si existe
//         const header = document.querySelector('.main-header');
//         if (header && headerBg) {
//             header.style.backgroundImage = `url('${headerBg}')`;
//             header.style.backgroundSize = 'cover';
//             header.style.backgroundPosition = 'center';
//             header.style.backgroundRepeat = 'no-repeat';
            
//             // Añadir clase para estilos adicionales con imagen
//             header.classList.add('has-bg-image');
//         }
//     }
    
//     // Aplicar tema inmediatamente para evitar FOUC (Flash of Unstyled Content)
//     applyThemeColors();
    
//     // También aplicar cuando el DOM esté completamente cargado
//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', applyThemeColors);
//     }
    
//     // Debug en desarrollo (remover en producción)
//     if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
//         console.log('Theme config:', { primaryColor, bgColor, headerBg });
//     }
// })();

// theme-init.js — versión segura respecto a preferencias de accesibilidad
(function(){
'use strict';


const root = document.documentElement;
const primaryColor = root.dataset.primaryColor || '#0645ad';
const bgColor = root.dataset.bgColor || '#ffffff';
const headerBg = root.dataset.headerBg || '';


// Preferencias del usuario (si las hubiere)
const userHc = localStorage.getItem('a11y_hc') === '1';
const userDark = localStorage.getItem('a11y_dark') === '1';


// Si el usuario pidió alto contraste, no pisamos variables críticas de HC.
if (!userHc) {
// Aplicamos branding solo si no hay HC activo.
root.style.setProperty('--brand', primaryColor);
root.style.setProperty('--page-bg', bgColor);
if (headerBg) root.style.setProperty('--theme-header-bg', headerBg);
} else {
// opcional: log en entorno dev
if (['localhost','127.0.0.1'].includes(window.location.hostname))
console.log('theme-init: HC activo, se omiten overrides de variables');
}


// Si hay preferencia dark, aplicamos la clase (para que CSS dark funcione).
if (userDark) {
root.classList.add('theme-dark');
root.classList.remove('theme-light');
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