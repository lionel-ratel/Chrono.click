
// Move the link when in article form
document.addEventListener('DOMContentLoaded', function() {
    const selectElement = document.getElementById('o_article_layout_type');
    const linkToMove = document.querySelector('a#btn-customizer');

    if (selectElement && linkToMove) {
        const targetContainer = selectElement.closest('.uk-inline');
        
        if (targetContainer) {
            targetContainer.appendChild(linkToMove);

            const style = document.createElement('style');
            style.textContent = `
                .uk-inline { display: flex !important; align-items: center; }
                a#btn-customizer {
                    margin-left: 10px;
                    display: none; /* Caché par défaut */
                    transition: all 0.3s ease;
                }
                a#btn-customizer.is-visible {
                    display: inline-flex;
                }
            `;
            document.head.appendChild(style);

            const toggleYooLink = () => {
                if (selectElement.value.includes('yootheme')) {
                    linkToMove.classList.add('is-visible');
                } else {
                    linkToMove.classList.remove('is-visible');
                }
            };

            selectElement.addEventListener('change', toggleYooLink);
            toggleYooLink();
        }
    }
});