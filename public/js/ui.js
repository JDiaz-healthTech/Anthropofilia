// === INICIO SNIPPET ui.js (reemplazar entero) ===
(function(){
  'use strict';
  var html = document.documentElement;

  // Estado
  var DEFAULT = { theme: 'light', hc: false, zoom: 100 }; // theme: 'light' | 'dark'

  function clamp(n, min, max){ return Math.max(min, Math.min(max, n)); }

  function read(){
    var theme = localStorage.getItem('theme') || DEFAULT.theme;
    var hc    = localStorage.getItem('a11y_hc') === '1';
    var zoom  = parseInt(localStorage.getItem('a11y_zoom') || String(DEFAULT.zoom), 10);
    zoom = clamp(isFinite(zoom) ? zoom : 100, 85, 160);
    return { theme, hc, zoom };
  }
  function write(st){
    localStorage.setItem('theme', st.theme === 'dark' ? 'dark' : 'light');
    localStorage.setItem('a11y_hc', st.hc ? '1' : '0');
    localStorage.setItem('a11y_zoom', String(st.zoom));
  }
  function apply(st){
    html.classList.toggle('theme-dark', st.theme === 'dark');
    html.classList.toggle('high-contrast', !!st.hc);
    html.style.setProperty('--font-zoom', String(st.zoom/100));

    // ARIA/disabled de los controles (si existen)
    var $ = (id)=>document.getElementById(id);
    var btnDark = $('toggle-dark') || $('themeToggle');
    var btnHC   = $('toggle-high-contrast') || $('contrastToggle');
    var btnInc  = $('increase-font-size') || $('fontInc');
    var btnDec  = $('decrease-font-size') || $('fontDec');
    var btnReset= $('fontReset');

    if (btnDark) btnDark.setAttribute('aria-pressed', String(st.theme === 'dark'));
    if (btnHC)   btnHC.setAttribute('aria-pressed',   String(st.hc));
    if (btnInc)  btnInc.disabled = st.zoom >= 160;
    if (btnDec)  btnDec.disabled = st.zoom <= 85;

    // (Opcional) marcar la combinación activa en la UI si tienes un selector de “4 temas”
    // por ejemplo data-current-theme="dark-hc" etc.
    html.dataset.activeScheme = (st.theme === 'dark' ? 'dark' : 'light') + (st.hc ? '-hc' : '');
  }
  function setState(patch){
    var next = Object.assign({}, read(), patch);
    next.theme = next.theme === 'dark' ? 'dark' : 'light';
    next.zoom  = clamp(next.zoom, 85, 160);
    write(next);
    apply(next);
    return next;
  }
  // API pública
  window.Prefs = { get: read, set: setState, apply };

  // Bindings
  document.addEventListener('DOMContentLoaded', function(){
    apply(read());
    var $ = (id)=>document.getElementById(id);

    var btnDark  = $('toggle-dark') || $('themeToggle');
    var btnHC    = $('toggle-high-contrast') || $('contrastToggle');
    var btnInc   = $('increase-font-size') || $('fontInc');
    var btnDec   = $('decrease-font-size') || $('fontDec');
    var btnReset = $('fontReset');

    if (btnDark)  btnDark.addEventListener('click', () => {
      var s = read(); setState({ theme: s.theme === 'dark' ? 'light' : 'dark' });
    });
    if (btnHC)    btnHC.addEventListener('click', () => setState({ hc: !read().hc }));
    if (btnInc)   btnInc.addEventListener('click', () => { var s=read(); setState({ zoom: s.zoom+5 }); });
    if (btnDec)   btnDec.addEventListener('click', () => { var s=read(); setState({ zoom: s.zoom-5 }); });
    if (btnReset) btnReset.addEventListener('click',  () => setState({ zoom: 100 }));

    // Menú móvil (si existe)
    var mobileMenuToggle = $('mobileMenuToggle');
    var siteNav = $('siteNav');
    if (mobileMenuToggle && siteNav){
      mobileMenuToggle.addEventListener('click', () => {
        var expanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
        mobileMenuToggle.setAttribute('aria-expanded', String(!expanded));
        mobileMenuToggle.classList.toggle('is-open', !expanded);
        siteNav.classList.toggle('active', !expanded);
      }, { passive:true });
    }
  });
})();
// === FIN SNIPPET ui.js ===
