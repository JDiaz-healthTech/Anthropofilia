document.addEventListener("DOMContentLoaded", () => {
  const btnDark = document.getElementById("toggle-dark");
  const btnHC = document.getElementById("toggle-high-contrast");
  const btnInc = document.getElementById("increase-font-size");
  const btnDec = document.getElementById("decrease-font-size");

  const html = document.documentElement;
  const body = document.body;

  // Persistencia en localStorage
  const load = () => {
    // Si nunca se guardó preferencia → arrancar en claro
    const darkPrefRaw = localStorage.getItem("a11y_dark");
    const darkPref =
      darkPrefRaw === null
        ? false
        : darkPrefRaw === "1" || darkPrefRaw === "true";
    return {
      dark: Boolean(darkPref),
      hc: localStorage.getItem("a11y_hc") === "1",
      zoom: Number.parseInt(localStorage.getItem("a11y_zoom") || "100", 10),
    };
  };

  const save = (st) => {
    localStorage.setItem("a11y_dark", st.dark ? "1" : "0");
    localStorage.setItem("a11y_hc", st.hc ? "1" : "0");
    localStorage.setItem("a11y_zoom", String(st.zoom));
  };

 const apply = (st) => {
  // Forzar una sola clase de tono (evita conflictos)
  if (st.dark) {
    html.classList.add('theme-dark');
    html.classList.remove('theme-light');
  } else {
    html.classList.remove('theme-dark');
    html.classList.add('theme-light');
  }

  // Alto contraste
  html.classList.toggle('high-contrast', !!st.hc);

  // Zoom
  html.style.fontSize = st.zoom + "%";

  // UI y debug
  updateButtonUI(st);
  console.log("Estado aplicado:", {
    dark: st.dark,
    hc: st.hc,
    zoom: st.zoom,
    htmlClasses: html.className,
    bodyClasses: body.className,
  });
};

  const updateButtonUI = (st) => {
    // Botón modo oscuro
    if (btnDark) {
      btnDark.setAttribute("aria-pressed", String(st.dark));
      btnDark.title = st.dark
        ? "Cambiar a modo claro"
        : "Cambiar a modo oscuro";
      btnDark.textContent = st.dark ? "☀️" : "🌗";
    }

    // Botón alto contraste
    if (btnHC) {
      btnHC.setAttribute("aria-pressed", String(st.hc));
      btnHC.title = st.hc
        ? "Desactivar alto contraste"
        : "Activar alto contraste";
      btnHC.textContent = st.hc ? "HC OFF" : "HC ON";
    }

    // Botones de zoom
    if (btnInc) {
      btnInc.title = `Aumentar tamaño de texto (actual: ${st.zoom}%)`;
      btnInc.disabled = st.zoom >= 160;
    }

    if (btnDec) {
      btnDec.title = `Reducir tamaño de texto (actual: ${st.zoom}%)`;
      btnDec.disabled = st.zoom <= 85;
    }
  };

  // Función para restringir el zoom entre límites
  const clamp = (n) => Math.max(85, Math.min(160, n));

  // Cargar y aplicar estado inicial
  let state = load();
  apply(state);

  // Event listeners para los botones
  if (btnDark) {
    btnDark.addEventListener("click", () => {
      state.dark = !state.dark;
      save(state);
      apply(state);
      console.log("Modo oscuro toggled:", state.dark);
    });
  }

  if (btnHC) {
    btnHC.addEventListener("click", () => {
      state.hc = !state.hc;
      save(state);
      apply(state);
      console.log("Alto contraste toggled:", state.hc);
    });
  }

  if (btnInc) {
    btnInc.addEventListener("click", () => {
      state.zoom = clamp(state.zoom + 5);
      save(state);
      apply(state);
      console.log("Zoom aumentado:", state.zoom + "%");
    });
  }

  if (btnDec) {
    btnDec.addEventListener("click", () => {
      state.zoom = clamp(state.zoom - 5);
      save(state);
      apply(state);
      console.log("Zoom reducido:", state.zoom + "%");
    });
  }

  // // Escuchar cambios en las preferencias del sistema
  // if (window.matchMedia) {
  //   const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
  //   darkModeQuery.addListener((e) => {
  //     // Solo cambiar si no hay preferencia guardada explícitamente
  //     if (!localStorage.getItem('a11y_dark')) {
  //       state.dark = e.matches;
  //       apply(state);
  //       console.log('Preferencia del sistema detectada:', e.matches ? 'oscuro' : 'claro');
  //     }
  //   });
  // }

  // Función de utilidad para debugging
  window.debugAccessibility = () => {
    console.log("Estado actual de accesibilidad:", {
      state: state,
      htmlClasses: html.className,
      bodyClasses: body.className,
      localStorage: {
        dark: localStorage.getItem("a11y_dark"),
        hc: localStorage.getItem("a11y_hc"),
        zoom: localStorage.getItem("a11y_zoom"),
      },
    });
  };
});
