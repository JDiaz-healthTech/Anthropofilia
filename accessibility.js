document.addEventListener('DOMContentLoaded', () => {
    const highContrastToggler = document.getElementById('toggle-high-contrast');
    const fontSizeIncrease = document.getElementById('increase-font-size');
    const fontSizeDecrease = document.getElementById('decrease-font-size');
    const body = document.body;
    const html = document.documentElement;

    // 1. High Contrast
    const applyHighContrast = (isHighContrast) => {
        if (isHighContrast) {
            body.classList.add('high-contrast');
        } else {
            body.classList.remove('high-contrast');
        }
    };

    highContrastToggler.addEventListener('click', () => {
        const isHighContrast = body.classList.toggle('high-contrast');
        localStorage.setItem('highContrast', isHighContrast);
    });

    // 2. Font Size
    const applyFontSize = (size) => {
        html.style.fontSize = size;
    };

    const getFontSize = () => {
        return parseFloat(getComputedStyle(html).fontSize);
    };

    fontSizeIncrease.addEventListener('click', () => {
        let currentSize = getFontSize();
        if (currentSize < 22) { // Max size limit
            let newSize = currentSize + 1;
            applyFontSize(`${newSize}px`);
            localStorage.setItem('fontSize', `${newSize}px`);
        }
    });

    fontSizeDecrease.addEventListener('click', () => {
        let currentSize = getFontSize();
        if (currentSize > 12) { // Min size limit
            let newSize = currentSize - 1;
            applyFontSize(`${newSize}px`);
            localStorage.setItem('fontSize', `${newSize}px`);
        }
    });

    // Apply preferences on load
    if (localStorage.getItem('highContrast') === 'true') {
        applyHighContrast(true);
    }

    const savedFontSize = localStorage.getItem('fontSize');
    if (savedFontSize) {
        applyFontSize(savedFontSize);
    }
});
