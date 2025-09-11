// Ligero lightbox + mejoras de imágenes
(function(){
  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  ready(function(){
    // Asegura lazy-loading en imágenes del contenido
    document.querySelectorAll('.contenido img, .imagen-destacada img').forEach(function(img){
      if(!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
      if(!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');
      if(!img.hasAttribute('sizes')) img.setAttribute('sizes', '(min-width: 800px) 720px, 100vw');
    });

// crear overlay sin innerHTML ===
var overlay = document.createElement('div');
overlay.className = 'lightbox-overlay';

var closeBtn = document.createElement('button');
closeBtn.className = 'lb-close';
closeBtn.setAttribute('aria-label', 'Cerrar');
closeBtn.appendChild(document.createTextNode('×'));

var imgEl = document.createElement('img');
imgEl.setAttribute('alt', '');

overlay.appendChild(closeBtn);
overlay.appendChild(imgEl);
document.body.appendChild(overlay);
// === FIN SNIPPET lightbox.js ===


    // Delegación: abrir al clickear imágenes dentro del contenido
    document.body.addEventListener('click', function(e){
      var img = e.target.closest('.contenido img, .imagen-destacada img');
      if (img) {
        e.preventDefault();
        open(img.currentSrc || img.src, img.alt);
      }
    });
  });
})();
// Fin lightbox.js