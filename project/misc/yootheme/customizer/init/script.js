(function() {
    const TARGET_PAGE = "[RATEL] - Home";

    const runAutoNav = () => {
        const pagesLink = document.querySelector('.yo-sidebar-content li[data-index="2"] a');
        
        alert("Plop");
        if (pagesLink) {
            pagesLink.click();
            waitForSelect();
        } else {
            setTimeout(runAutoNav, 500);
        }
    };

    const waitForSelect = () => {
        const checkSelect = setInterval(() => {
            const selectEl = document.querySelector('.yo-sidebar-content select.uk-select');
            
            if (selectEl) {
                clearInterval(checkSelect);
                selectEl.value = "*";
                selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                waitForPage();
            }
        }, 200);
    };

    const waitForPage = () => {
        const checkPage = setInterval(() => {
            const links = document.querySelectorAll('.yo-sidebar-content a');
            const target = Array.from(links).find(el => el.textContent.includes(TARGET_PAGE));

            if (target) {
                clearInterval(checkPage);
                target.click();
                waitForBuilderButton();
            }
        }, 200);
    };

    const waitForBuilderButton = () => {
        const checkBuilder = setInterval(() => {
            const btn = Array.from(document.querySelectorAll('a, button'))
                             .find(el => el.textContent.trim() === "OUVRIR LE MODÈLE");
            if (btn) {
                clearInterval(checkBuilder);
                btn.click();
            }
        }, 300);
    };

    runAutoNav();
})();