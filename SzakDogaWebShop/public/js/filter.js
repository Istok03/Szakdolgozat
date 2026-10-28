window.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('filterToggle');
    const panel = document.getElementById('filterPanel');

    if (toggleBtn && panel) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.toggle('show');
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


