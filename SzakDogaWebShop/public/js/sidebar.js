document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('adminSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (sidebar && toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('visible');
        });

        document.addEventListener('click', (e) => {
            const clickedInsideSidebar = sidebar.contains(e.target);
            const clickedToggle = toggleBtn.contains(e.target);

            if (!clickedInsideSidebar && !clickedToggle) {
                sidebar.classList.remove('visible');
            }
        });
    }
});
