const desktopSidebar = document.getElementById('desktop-sidebar');
const mobileSidebar = document.getElementById('mobile-sidebar');
const sidebarPanel = document.getElementById('sidebar-panel');
const sidebarOpen = document.getElementById('sidebar-open');
const sidebarClose = document.getElementById('sidebar-close');
const desktopLayout = window.matchMedia('(min-width: 1024px)');

function syncSidebar() {
    if (desktopLayout.matches) {
        if (mobileSidebar.open) {
            mobileSidebar.close();
        }

        desktopSidebar.append(sidebarPanel);
    } else {
        mobileSidebar.append(sidebarPanel);
    }
}

sidebarOpen.addEventListener('click', () => {
    mobileSidebar.showModal();
    sidebarOpen.setAttribute('aria-expanded', 'true');
    document.documentElement.classList.add('overflow-hidden');
});

sidebarClose.addEventListener('click', () => mobileSidebar.close());

mobileSidebar.addEventListener('click', (event) => {
    if (event.target === mobileSidebar) {
        mobileSidebar.close();
    }
});

mobileSidebar.addEventListener('close', () => {
    sidebarOpen.setAttribute('aria-expanded', 'false');
    document.documentElement.classList.remove('overflow-hidden');
});

desktopLayout.addEventListener('change', syncSidebar);
syncSidebar();

const signatureButton = document.getElementById('signature-menu-btn');

if (signatureButton) {
    const signatureSubmenu = document.getElementById('signature-submenu');
    const signatureChevron = document.getElementById('signature-chevron');

    signatureButton.addEventListener('click', () => {
        const expanded = signatureButton.getAttribute('aria-expanded') !== 'true';

        signatureSubmenu.classList.toggle('flex', expanded);
        signatureSubmenu.classList.toggle('hidden', !expanded);
        signatureChevron.classList.toggle('rotate-180', expanded);
        signatureButton.setAttribute('aria-expanded', String(expanded));
    });
}
