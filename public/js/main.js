
(function applySavedTheme() {
    try {
        const validThemes = ['google-light', 'indigo', 'rose'];
        let savedTheme = localStorage.getItem('aether_theme') || 'google-light';
        if (!validThemes.includes(savedTheme)) {
            savedTheme = 'google-light';
            localStorage.setItem('aether_theme', 'google-light');
        }
        document.documentElement.setAttribute('data-theme', savedTheme);
    } catch (e) {}
})();

function selectTheme(themeKey) {
    const validThemes = ['google-light', 'indigo', 'rose'];
    if (!themeKey || !validThemes.includes(themeKey)) return;
    const currentTheme = document.documentElement.getAttribute('data-theme');
    if (currentTheme === themeKey) {
        return;
    }
    document.documentElement.setAttribute('data-theme', themeKey);
    try {
        localStorage.setItem('aether_theme', themeKey);
    } catch (e) {}

    document.querySelectorAll('.theme-option-card').forEach(card => {
        if (card.getAttribute('data-theme-key') === themeKey) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    });

    const themeNameMap = {
        'google-light': 'Google Светлая',
        'indigo': 'Индиго стратосфера',
        'rose': 'Коралловый закат'
    };

    const currentThemeLabel = document.getElementById('currentThemeLabel');
    if (currentThemeLabel) {
        currentThemeLabel.textContent = themeNameMap[themeKey] || themeKey;
    }

    showToast('Палитра оформления изменена: ' + (themeNameMap[themeKey] || themeKey), 'info');
}
window.selectTheme = selectTheme;

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('is-open');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}
window.openModal = openModal;

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.classList.remove('active');
    if (!document.querySelector('.modal-backdrop.is-open, .modal-backdrop.active')) {
        document.body.style.overflow = '';
    }
}
window.closeModal = closeModal;

function openThemeModal() {
    openModal('themeModal');
}
window.openThemeModal = openThemeModal;

function closeThemeModal() {
    closeModal('themeModal');
}
window.closeThemeModal = closeThemeModal;

let lastToastMessage = '';
let lastToastTime = 0;

function showToast(message, type = 'info', duration = 3500) {
    const now = Date.now();
    if (message === lastToastMessage && (now - lastToastTime) < 1500) {
        return;
    }
    lastToastMessage = message;
    lastToastTime = now;

    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        container.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-card toast-${type}`;

    let bg = '#3b82f6';
    let icon = 'ℹ';
    if (type === 'success') { bg = '#10b981'; icon = '✓'; }
    if (type === 'error' || type === 'warning') { bg = '#ef4444'; icon = '✕'; }

    toast.style.cssText = `background: ${bg}; color: #fff; padding: 14px 20px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; font-weight: 500; font-size: 14px; pointer-events: auto; transition: all 0.3s ease; opacity: 0; transform: translateY(10px);`;
    toast.innerHTML = `<span style="font-weight: 700;">${icon}</span><span>${message}</span>`;

    container.appendChild(toast);
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}
window.showToast = showToast;

document.addEventListener('DOMContentLoaded', () => {
    initThemeSystem();
    initMobileNav();
    initModals();
});

function initThemeSystem() {
    const currentTheme = localStorage.getItem('aether_theme') || 'google-light';

    document.querySelectorAll('.theme-option-card').forEach(card => {
        const key = card.getAttribute('data-theme-key');
        if (key === currentTheme) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
        card.onclick = (e) => {
            e.preventDefault();
            selectTheme(key);
        };
    });

    document.querySelectorAll('.ag-announcement-close').forEach(btn => {
        btn.addEventListener('click', () => {
            const bar = btn.closest('.ag-announcement-bar');
            if (bar) bar.style.display = 'none';
        });
    });
}

function initMobileNav() {
    const toggle = document.getElementById('mobileNavToggle');
    const drawer = document.getElementById('mobileNavDrawer');

    if (!toggle || !drawer) return;

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        drawer.classList.toggle('is-open');
    });

    document.addEventListener('click', (e) => {
        if (!drawer.contains(e.target) && !toggle.contains(e.target)) {
            drawer.classList.remove('is-open');
        }
    });
}

function initModals() {

    document.querySelectorAll('[data-modal]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = trigger.getAttribute('data-modal');
            openModal(modalId);
        });
    });

    document.querySelectorAll('.modal-close-btn, [data-close-modal]').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = closeBtn.closest('.modal-backdrop');
            if (modal) closeModal(modal.id);
        });
    });

    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal.id);
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const openModalEl = document.querySelector('.modal-backdrop.is-open, .modal-backdrop.active');
            if (openModalEl) closeModal(openModalEl.id);
        }
    });
}
