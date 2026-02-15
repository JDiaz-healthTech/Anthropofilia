/**
 * personalizar.js
 *
 * Preview en vivo de la imagen de cabecera y slider de overlay.
 * Solo se carga en personalizar.php.
 */
(function () {
    'use strict';

    var preview = document.getElementById('header-preview');
    var overlay = document.getElementById('preview-overlay');
    var slider = document.getElementById('header_overlay_opacity');
    var valueLabel = document.getElementById('overlay-value');
    var fileInput = document.getElementById('header_image');
    var fileInfo = document.getElementById('file-info');

    if (!preview || !overlay || !slider) return;

    // =========================================================
    // 1. Inicializar preview con imagen actual (si existe)
    // =========================================================
    var header = document.querySelector('.main-header.has-bg-image');
    if (header) {
        var bgImage = header.style.backgroundImage;
        if (bgImage) {
            preview.style.backgroundImage = bgImage;
            preview.classList.add('has-image');
        }
    }

    // =========================================================
    // 2. Slider de overlay — actualiza preview en tiempo real
    // =========================================================
    function updateOverlay(value) {
        var decimal = value / 100;
        var top = (decimal * 0.8).toFixed(2);
        var bottom = Math.min(decimal * 1.2, 1).toFixed(2);

        overlay.style.background =
            'linear-gradient(to bottom, rgba(0,0,0,' + top + ') 0%, rgba(0,0,0,' + bottom + ') 100%)';

        valueLabel.textContent = value + '%';
    }

    // Inicializar con valor actual
    updateOverlay(parseInt(slider.value, 10));

    slider.addEventListener('input', function () {
        updateOverlay(parseInt(this.value, 10));
    });

    // =========================================================
    // 3. Preview de imagen seleccionada (antes de subir)
    // =========================================================
    fileInput.addEventListener('change', function () {
        var file = this.files[0];
        if (!file) return;

        // Validar tipo
        var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (allowed.indexOf(file.type) === -1) {
            fileInfo.textContent = 'Formato no válido. Usa JPG, PNG, GIF o WebP.';
            fileInfo.style.color = '#dc3545';
            return;
        }

        // Validar tamaño
        if (file.size > 2 * 1024 * 1024) {
            fileInfo.textContent = 'La imagen supera los 2MB.';
            fileInfo.style.color = '#dc3545';
            return;
        }

        // Mostrar info del archivo
        var sizeMB = (file.size / (1024 * 1024)).toFixed(1);
        fileInfo.textContent = file.name + ' (' + sizeMB + ' MB)';
        fileInfo.style.color = '';

        // Preview con FileReader
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.style.backgroundImage = 'url(' + e.target.result + ')';
            preview.classList.add('has-image');

            // Quitar mensaje "Sin imagen" si existe
            var empty = preview.querySelector('.pz-preview__empty');
            if (empty) empty.remove();
        };
        reader.readAsDataURL(file);
    });

    // =========================================================
    // 4. Hover en historial — preview temporal
    // =========================================================
    var historyItems = document.querySelectorAll('.pz-history__item');
    var originalBg = preview.style.backgroundImage;

    historyItems.forEach(function (item) {
        var img = item.querySelector('img');
        if (!img) return;

        item.addEventListener('mouseenter', function () {
            originalBg = preview.style.backgroundImage;
            preview.style.backgroundImage = 'url(' + img.src + ')';
            if (!preview.classList.contains('has-image')) {
                preview.classList.add('has-image');
            }
        });

        item.addEventListener('mouseleave', function () {
            preview.style.backgroundImage = originalBg;
            if (!originalBg) {
                preview.classList.remove('has-image');
            }
        });
    });
})();
