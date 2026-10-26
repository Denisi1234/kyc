const drawer = document.getElementById('pc-drawer');
const openButton = document.querySelector('[data-pc-menu-open]');
const closeButtons = document.querySelectorAll('[data-pc-menu-close]');
const body = document.body;
const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
let lastFocused = null;

const getDrawerFocusables = () => Array.from(drawer?.querySelectorAll(focusableSelector) ?? [])
    .filter((element) => element.getClientRects().length > 0);

const openDrawer = () => {
    if (!drawer) return;
    lastFocused = document.activeElement instanceof HTMLElement ? document.activeElement : null;
    drawer.removeAttribute('hidden');
    drawer.setAttribute('aria-hidden', 'false');
    drawer.classList.add('is-open');
    body.classList.add('pc-drawer-open');
    openButton?.setAttribute('aria-expanded', 'true');
    openButton?.setAttribute('aria-label', 'Close menu');
    requestAnimationFrame(() => drawer.querySelector('.pc-drawer__close')?.focus());
};

const closeDrawer = ({ restoreFocus = true } = {}) => {
    if (!drawer) return;
    drawer.classList.remove('is-open');
    drawer.setAttribute('hidden', '');
    drawer.setAttribute('aria-hidden', 'true');
    body.classList.remove('pc-drawer-open');
    openButton?.setAttribute('aria-expanded', 'false');
    openButton?.setAttribute('aria-label', 'Open menu');

    if (restoreFocus) {
        const target = lastFocused && lastFocused !== body && document.contains(lastFocused)
            ? lastFocused
            : openButton;
        target?.focus();
    } else if (drawer.contains(document.activeElement)) {
        document.activeElement.blur();
    }
};

openButton?.addEventListener('click', openDrawer);

closeButtons.forEach((button) => {
    button.addEventListener('click', () => closeDrawer());
});

drawer?.querySelectorAll('a[href]').forEach((link) => {
    link.addEventListener('click', () => closeDrawer({ restoreFocus: false }));
});

document.addEventListener('keydown', (event) => {
    if (!drawer?.classList.contains('is-open')) return;

    if (event.key === 'Escape') {
        event.preventDefault();
        closeDrawer();
        return;
    }

    if (event.key !== 'Tab') return;

    const focusables = getDrawerFocusables();

    if (!focusables.length) {
        event.preventDefault();
        return;
    }

    const first = focusables[0];
    const last = focusables[focusables.length - 1];

    if (!drawer.contains(document.activeElement)) {
        event.preventDefault();
        first.focus();
        return;
    }

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth > 980 && drawer?.classList.contains('is-open')) {
        closeDrawer({ restoreFocus: false });
    }
});

document.querySelectorAll('[data-pc-newsletter]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const input = form.querySelector('input[type="email"]');
        const note = form.parentElement?.querySelector('[data-pc-newsletter-note]');
        const value = (input?.value || '').trim();

        if (!input || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
            input?.setAttribute('aria-invalid', 'true');
            input?.focus();
            if (note) {
                note.textContent = 'Please enter a valid email address.';
                note.hidden = false;
            }
            return;
        }

        input.removeAttribute('aria-invalid');
        form.reset();
        if (note) {
            note.textContent = 'Thank you. You are on the list for rehearsal reminders and concert news.';
            note.hidden = false;
        }
    });
});
