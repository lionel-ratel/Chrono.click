<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

if ( !isset( $options ) ) {
    return false;
}

$app	=	Factory::getApplication();

if ( !$app->input->getInt( 'f', 0 ) || $app->input->getString( 'p', '' ) !== 'customizer' ) {
	return;
}

$js		=	'
(function() {
    const runAutoNav = () => {
        const pagesLink = document.querySelector(\'.yo-sidebar-content li[data-index="2"] a\');
        if (pagesLink) {
            pagesLink.click();
            waitForBuilderButton();
        } else {
            setTimeout(runAutoNav, 500);
        }
    };

    const waitForBuilderButton = () => {
        const checkBuilder = setInterval(() => {
            const btn = Array.from(document.querySelectorAll("button"))
                .find(el => el.textContent.trim().toUpperCase() === "BUILDER");
            if (btn) {
                clearInterval(checkBuilder);
                btn.click();
            }
        }, 300);
    };

    const updateBreadcrumbVisibility = () => {
        const breadcrumbLink = document.querySelector(".yo-sidebar-breadcrumb a.uk-h4");
        if (!breadcrumbLink) return;

        const span = breadcrumbLink.querySelector("span");
        if (span) {
            const text = span.textContent.trim().toUpperCase();
            
            if (text === "PAGES") {
                breadcrumbLink.style.setProperty("display", "none", "important");
            } else {
                breadcrumbLink.style.setProperty("display", "flex", "important");
            }
        }
    };

    const startObserver = () => {
        if (!document.body) {
            setTimeout(startObserver, 100);
            return;
        }

        const observer = new MutationObserver(() => {
            updateBreadcrumbVisibility();
        });

        observer.observe(document.body, { 
            childList: true, 
            subtree: true, 
            characterData: true 
        });
    };

    runAutoNav();
    startObserver();
})();
';

Factory::getDocument()->addScriptDeclaration( $js );
?>