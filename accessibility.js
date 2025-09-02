// document.addEventListener('DOMContentLoaded', () => {
//     const highContrastToggler = document.getElementById('toggle-high-contrast');
//     const fontSizeIncrease = document.getElementById('increase-font-size');
//     const fontSizeDecrease = document.getElementById('decrease-font-size');
//     const body = document.body;
//     const html = document.documentElement;

//     // 1. High Contrast
//     const applyHighContrast = (isHighContrast) => {
//         if (isHighContrast) {
//             body.classList.add('high-contrast');
//         } else {
//             body.classList.remove('high-contrast');
//         }
//     };

//     highContrastToggler.addEventListener('click', () => {
//         const isHighContrast = body.classList.toggle('high-contrast');
//         localStorage.setItem('highContrast', isHighContrast);
//     });

//     // 2. Font Size
//     const applyFontSize = (size) => {
//         html.style.fontSize = size;
//     };

//     const getFontSize = () => {
//         return parseFloat(getComputedStyle(html).fontSize);
//     };

//     fontSizeIncrease.addEventListener('click', () => {
//         let currentSize = getFontSize();
//         if (currentSize < 22) { // Max size limit
//             let newSize = currentSize + 1;
//             applyFontSize(`${newSize}px`);
//             localStorage.setItem('fontSize', `${newSize}px`);
//         }
//     });

//     fontSizeDecrease.addEventListener('click', () => {
//         let currentSize = getFontSize();
//         if (currentSize > 12) { // Min size limit
//             let newSize = currentSize - 1;
//             applyFontSize(`${newSize}px`);
//             localStorage.setItem('fontSize', `${newSize}px`);
//         }
//     });

//     // Apply preferences on load
//     if (localStorage.getItem('highContrast') === 'true') {
//         applyHighContrast(true);
//     }

//     const savedFontSize = localStorage.getItem('fontSize');
//     if (savedFontSize) {
//         applyFontSize(savedFontSize);
//     }
// });
// accessibility.js (versión limpia y compatible con tus IDs actuales)
document.addEventListener('DOMContentLoaded', () => {
  const btnDark = document.getElementById('toggle-dark');
  const btnHC   = document.getElementById('toggle-high-contrast');
  const btnInc  = document.getElementById('increase-font-size');
  const btnDec  = document.getElementById('decrease-font-size');

  const html = document.documentElement;
  const body = document.body;

  // Persistencia
  const load = () => {
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    return {
      dark: (localStorage.getItem('a11y_dark') ?? (prefersDark ? '1' : '0')) === '1',
      hc:   localStorage.getItem('a11y_hc')   === '1',
      zoom: parseInt(localStorage.getItem('a11y_zoom') || '100', 10)
    };
  };
  const save = (st) => {
    localStorage.setItem('a11y_dark', st.dark ? '1' : '0');
    localStorage.setItem('a11y_hc',   st.hc   ? '1' : '0');
    localStorage.setItem('a11y_zoom', String(st.zoom));
  };

  const apply = (st) => {
    html.classList.toggle('theme-dark', st.dark);
    body.classList.toggle('high-contrast', st.hc);
    html.style.fontSize = st.zoom + '%';

    // Accesibilidad aria-pressed + etiquetas
    if (btnDark) {
      btnDark.setAttribute('aria-pressed', String(st.dark));
      btnDark.title = 'Alternar claro/oscuro';
      btnDark.textContent = st.dark ? '☀️' : '🌗';
    }
    if (btnHC) {
      btnHC.setAttribute('aria-pressed', String(st.hc));
      btnHC.title = 'Alternar alto contraste';
      btnHC.textContent = st.hc ? 'HC off' : 'HC';
    }
  };

  const clamp = (n) => Math.max(85, Math.min(160, n));

  let state = load();
  apply(state);

  // Eventos
  if (btnDark) btnDark.addEventListener('click', () => { state.dark = !state.dark; save(state); apply(state); });
  if (btnHC)   btnHC.addEventListener('click',   () => { state.hc   = !state.hc;   save(state); apply(state); });
  if (btnInc)  btnInc.addEventListener('click',  () => { state.zoom = clamp(state.zoom + 5); save(state); apply(state); });
  if (btnDec)  btnDec.addEventListener('click',  () => { state.zoom = clamp(state.zoom - 5); save(state); apply(state); });
});
