// js/slugify.js — Auto-slug compartido entre crear_pagina y editar_pagina
(function () {
    'use strict';

    var t = document.getElementById('titulo');
    var s = document.getElementById('slug');
    if (!t || !s) return;

    var userEditedSlug = false;
    // En edición el slug ya tiene valor; solo autogenerar si empieza vacío
    var hadInitialSlug = !!s.value;

    s.addEventListener('input', function () {
        userEditedSlug = true;
    });

    function slugify(str) {
        return str
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .substring(0, 150);
    }

    t.addEventListener('input', function () {
        if (userEditedSlug) return;
        // Crear: siempre autogenera. Editar: solo si slug empezó vacío.
        if (!hadInitialSlug && t.value) {
            s.value = slugify(t.value);
        }
    });
})();
