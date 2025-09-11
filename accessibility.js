// accessibility.js -- Accesibilidad: tema, contraste, zoom
document.addEventListener('DOMContentLoaded', function(){
  if (!window.Prefs) return; // ui.js expone la API unificada

  // Compatibilidad con IDs históricos
  var $ = function(id){ return document.getElementById(id); };
  var btnDark = $('toggle-dark');
  var btnHC   = $('toggle-high-contrast');
  var btnInc  = $('increase-font-size');
  var btnDec  = $('decrease-font-size');

  if (btnDark) btnDark.addEventListener('click', function(){ Prefs.set({ dark: !Prefs.get().dark }); });
  if (btnHC)   btnHC.addEventListener('click',   function(){ Prefs.set({ hc: !Prefs.get().hc }); });
  if (btnInc)  btnInc.addEventListener('click',  function(){ var s=Prefs.get(); Prefs.set({ zoom: Math.min(160, s.zoom+5) }); });
  if (btnDec)  btnDec.addEventListener('click',  function(){ var s=Prefs.get(); Prefs.set({ zoom: Math.max(85, s.zoom-5) }); });
});
