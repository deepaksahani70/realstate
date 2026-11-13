
const menuToggle = document.getElementById('adminMenuToggle');
const adminOverlay = document.getElementById('adminOverlay');

function closeAdminSidebar() {
    document.body.classList.remove('sidebar-open');
    menuToggle?.setAttribute('aria-expanded', 'false');
}

menuToggle?.addEventListener('click', () => {
    const isOpen = document.body.classList.toggle('sidebar-open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
});

adminOverlay?.addEventListener('click', closeAdminSidebar);

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeAdminSidebar();
    }
});
