// === Utilidades cookies ===
function getCookie(name){
  const m = document.cookie.match(new RegExp('(^| )'+name+'=([^;]+)'));
  return m ? decodeURIComponent(m[2]) : null;
}
function setCookie(name, value, maxAgeSec=31536000){
  document.cookie = name + "=" + encodeURIComponent(value) + "; Path=/; Max-Age=" + maxAgeSec + "; SameSite=Lax";
}

// === Preferencias ===
const DEFAULT_PREFS = { theme: 'light', contrast: 'normal', fontScale: '100' };

function readPrefs(){
  try {
    const raw = getCookie('prefs');
    if (!raw) return { ...DEFAULT_PREFS };
    const parsed = JSON.parse(raw);
    return { ...DEFAULT_PREFS, ...(parsed || {}) };
  } catch {
    return { ...DEFAULT_PREFS };
  }
}

function writePrefs(p){
  setCookie('prefs', JSON.stringify(p));
}

function applyPrefs(p){
  const html = document.documentElement;
  html.classList.toggle('theme-dark', p.theme === 'dark');
  html.classList.toggle('theme-light', p.theme !== 'dark');
  html.classList.toggle('contrast-high', p.contrast === 'high');
  html.classList.toggle('contrast-normal', p.contrast !== 'high');
  // limpia clases font-*
  [...html.classList].forEach(c => { if (c.startsWith('font-')) html.classList.remove(c); });
  const fs = String(p.fontScale).replace(/[^0-9]/g,'') || '100';
  html.classList.add('font-'+fs);
}

function setPrefs(patch){
  const current = readPrefs();
  const next = { ...current, ...patch };
  writePrefs(next);
  applyPrefs(next);
}

function clampFontScale(n){
  // límites: 85% .. 140% en pasos de 5
  const min = 85, max = 140;
  const step = 5;
  n = Math.round(n / step) * step;
  if (n < min) n = min;
  if (n > max) n = max;
  return String(n);
}

// === Handlers UI ===
function bindControls(){
  const themeToggle = document.getElementById('themeToggle');
  const contrastToggle = document.getElementById('contrastToggle');
  const fontInc = document.getElementById('fontInc');
  const fontDec = document.getElementById('fontDec');
  const fontReset = document.getElementById('fontReset');
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const siteNav = document.getElementById('siteNav');

  if (themeToggle){
    themeToggle.addEventListener('click', () => {
      const p = readPrefs();
      setPrefs({ theme: p.theme === 'dark' ? 'light' : 'dark' });
    });
  }
  if (contrastToggle){
    contrastToggle.addEventListener('click', () => {
      const p = readPrefs();
      setPrefs({ contrast: p.contrast === 'high' ? 'normal' : 'high' });
    });
  }
  if (fontInc){
    fontInc.addEventListener('click', () => {
      const p = readPrefs();
      setPrefs({ fontScale: clampFontScale(parseInt(p.fontScale, 10) + 5) });
    });
  }
  if (fontDec){
    fontDec.addEventListener('click', () => {
      const p = readPrefs();
      setPrefs({ fontScale: clampFontScale(parseInt(p.fontScale, 10) - 5) });
    });
  }
  if (fontReset){
    fontReset.addEventListener('click', () => {
      setPrefs({ fontScale: '100' });
    });
  }
  if (mobileMenuToggle && siteNav){
    mobileMenuToggle.addEventListener('click', () => {
      const expanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
      mobileMenuToggle.setAttribute('aria-expanded', String(!expanded));
      siteNav.classList.toggle('open', !expanded);
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // Aplica por si la cookie se modificó client-side; SSR ya puso clases base
  applyPrefs(readPrefs());
  bindControls();
});
