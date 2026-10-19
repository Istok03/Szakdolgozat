document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('menuToggle');
    const links = document.getElementById('navLinks');
    if (!toggle || !links) return;
    const setOpen = open => {
        links.classList.toggle('active', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Menü bezárása' : 'Menü megnyitása');
    };
    toggle.addEventListener('click', () => setOpen(!links.classList.contains('active')));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && links.classList.contains('active')) {
            setOpen(false);
            toggle.focus();
        }
    });
});