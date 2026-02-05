window.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('filterToggle');
    const panel = document.getElementById('filterPanel');
    const applyBtn = document.getElementById('applyFilter');

    if (toggleBtn && panel) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.toggle('show');
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', () => {
            const name = document.getElementById('filterName').value;
            const min = document.getElementById('filterMin').value;
            const max = document.getElementById('filterMax').value;
            const sale = document.getElementById('filterSale').checked ? 1 : 0;

            const params = new URLSearchParams({
                name: name,
                min: min,
                max: max,
                sale: sale
            });

            window.location.href = `/products?${params.toString()}`;
        });
    }

    if (panel && toggleBtn) {
        document.addEventListener('click', (e) => {
            if (!panel.contains(e.target) && e.target !== toggleBtn) {
                panel.classList.remove('show');
            }
        });
    }

    const categoryToggle = document.getElementById('categoryToggle');
    const categoryPanel = document.getElementById('categoryPanel');

    if (categoryToggle && categoryPanel) {
        categoryToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            categoryPanel.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!categoryPanel.contains(e.target) && e.target !== categoryToggle) {
                categoryPanel.classList.remove('show');
            }
        });
    }

});


