// theme-init.js - Inicialización del tema dinámico
(function() {
    'use strict';
    
    // Obtener configuración desde data attributes del HTML
    const html = document.documentElement;
    const primaryColor = html.dataset.primaryColor || '#0645ad';
    const bgColor = html.dataset.bgColor || '#ffffff';
    const headerBg = html.dataset.headerBg || '';
    
    // Aplicar variables CSS dinámicamente
    function applyThemeColors() {
        const root = document.documentElement;
        root.style.setProperty('--brand', primaryColor);
        root.style.setProperty('--page-bg', bgColor);
        
        // Aplicar imagen de header si existe
        const header = document.querySelector('.main-header');
        if (header && headerBg) {
            header.style.backgroundImage = `url('${headerBg}')`;
            header.style.backgroundSize = 'cover';
            header.style.backgroundPosition = 'center';
            header.style.backgroundRepeat = 'no-repeat';
            
            // Añadir clase para estilos adicionales con imagen
            header.classList.add('has-bg-image');
        }
    }
    
    // Aplicar tema inmediatamente para evitar FOUC (Flash of Unstyled Content)
    applyThemeColors();
    
    // También aplicar cuando el DOM esté completamente cargado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyThemeColors);
    }
    
    // Debug en desarrollo (remover en producción)
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('Theme config:', { primaryColor, bgColor, headerBg });
    }
})();