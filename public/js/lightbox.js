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

    // Crea overlay
    var overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.innerHTML = '<button class="lb-close" aria-label="Cerrar">×</button><img alt="">';
    document.body.appendChild(overlay);

    var imgEl = overlay.querySelector('img');
    function open(src, alt){
      imgEl.src = src;
      imgEl.alt = alt || '';
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
    function close(){
      overlay.classList.remove('open');
      imgEl.removeAttribute('src');
      document.body.style.overflow = '';
    }

    overlay.addEventListener('click', function(e){
      if (e.target === overlay || e.target.classList.contains('lb-close')) close();
    });
    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape') close();
    });

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