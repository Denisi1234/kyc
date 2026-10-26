import '@fontsource/inter/400.css';
import '@fontsource/inter/500.css';
import '@fontsource/inter/600.css';
import '@fontsource/inter/700.css';
import './bootstrap';

import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

const body = document.body;
const sidebar = document.getElementById('portal-sidebar');
const mobileMenu = document.querySelector('[data-sidebar-toggle]');

const closeSidebar = () => {
    body.classList.remove('sidebar-open');
    mobileMenu?.setAttribute('aria-expanded', 'false');
};

const openSidebar = () => {
    body.classList.add('sidebar-open');
    mobileMenu?.setAttribute('aria-expanded', 'true');
};

mobileMenu?.addEventListener('click', () => {
    if (body.classList.contains('sidebar-open')) {
        closeSidebar();
    } else {
        openSidebar();
    }
});

document.querySelectorAll('[data-sidebar-close]').forEach((element) => {
    element.addEventListener('click', closeSidebar);
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeSidebar();
    }
});

sidebar?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 992) {
            closeSidebar();
        }
    });
});

window.addEventListener('resize', () => {
    if (window.innerWidth >= 992) {
        closeSidebar();
    }
});

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('.alert[data-auto-dismiss]').forEach((alert) => {
    window.setTimeout(() => {
        bootstrap.Alert.getOrCreateInstance(alert).close();
    }, 6000);
});

document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = document.getElementById(button.getAttribute('data-toggle-password'));
        if (!target) return;
        const isPassword = target.getAttribute('type') === 'password';
        target.setAttribute('type', isPassword ? 'text' : 'password');
        button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        button.textContent = isPassword ? 'Hide' : 'Show';
    });
});

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
    bootstrap.Tooltip.getOrCreateInstance(element);
});
