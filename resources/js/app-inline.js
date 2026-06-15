// ============================================
// Global Utility Functions (attached to window)
// ============================================

window.setButtonLoading = function(button, label) {
    if (!button || button.dataset.loading === '1') return;
    button.dataset.loading = '1';
    button.dataset.originalHtml = button.innerHTML;
    button.disabled = true;
    button.classList.add('opacity-70', 'cursor-not-allowed');
    button.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> ' + label + '</span>';
};

window.resetButtonLoading = function(button) {
    if (!button) return;
    button.disabled = false;
    button.classList.remove('opacity-70', 'cursor-not-allowed');
    if (button.dataset.originalHtml) button.innerHTML = button.dataset.originalHtml;
    button.dataset.loading = '0';
};

window.showSystemMessage = function(type, message) {
    const colors = {
        success: 'border-success-500 bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
        error: 'border-error-500 bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
        warning: 'border-warning-500 bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400'
    };
    const alert = document.createElement('div');
    alert.className = 'mb-4 flex w-full border-l-6 px-7 py-5 shadow-theme-md ' + (colors[type] || colors.error);
    alert.setAttribute('role', 'alert');
    alert.innerHTML = '<p class="text-sm font-medium">' + message + '</p>';
    const mainContent = document.querySelector('main .mx-auto');
    if (mainContent) {
        const heading = mainContent.querySelector('.mb-4');
        if (heading?.nextSibling) {
            mainContent.insertBefore(alert, heading.nextSibling);
        } else {
            mainContent.prepend(alert);
        }
    } else {
        document.body.prepend(alert);
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

window.showLoadingOverlay = function(message) {
    let overlay = document.getElementById('globalLoadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'globalLoadingOverlay';
        overlay.className = 'fixed inset-0 z-999999 flex items-center justify-center bg-gray-900/50 px-4';
        overlay.innerHTML = '<div class="rounded-2xl bg-white p-6 text-center shadow-theme-xl dark:bg-gray-900"><div class="mx-auto mb-4 h-12 w-12 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div><p class="text-sm font-medium text-gray-700 dark:text-gray-300"></p></div>';
        document.body.appendChild(overlay);
    }
    overlay.querySelector('p').textContent = message || 'Memproses...';
    overlay.classList.remove('hidden');
};

window.hideLoadingOverlay = function() {
    const overlay = document.getElementById('globalLoadingOverlay');
    if (overlay) overlay.classList.add('hidden');
};

// ============================================
import { Indonesian } from 'flatpickr/dist/l10n/id.js';

// ... existing code ...

// Init on DOM ready
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Flatpickr
    window.flatpickr('.datepicker', {
        locale: Indonesian,
        dateFormat: 'Y-m-d',
        altFormat: 'd/m/Y',
        altInput: true,
        zIndex: 9999,
        direction: "up",
        disableMobile: true,
        position: "right",
        prevArrow: '<svg class="stroke-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.25 6L9 12.25L15.25 18.5" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        nextArrow: '<svg class="stroke-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.75 19L15 12.75L8.75 6.5" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
    });

    // TomSelect
    document.querySelectorAll('select.select2, select.barangSelect').forEach(function(el) {
        if (!el.getAttribute('data-placeholder')) {
            var opt = el.querySelector('option[value=""]');
            if (opt) el.setAttribute('data-placeholder', opt.textContent.trim());
        }
        if (!el.tomselect) {
            new window.TomSelect(el, {
                placeholder: el.getAttribute('data-placeholder') || 'Pilih...',
                maxItems: 1,
                searchField: ['text', 'value'],
                dropdownParent: 'body',
                plugins: {
                    clear_button: { title: 'Hapus pilihan' }
                }
            });
        }
    });
});

// ============================================
// Global event listeners
// ============================================
document.addEventListener('submit', function (event) {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.dataset.protectSubmit !== 'true') return;
    if (form.dataset.confirmMessage && form.dataset.confirmed !== 'true') return;
    const submitButton = form.querySelector('[type="submit"]');
    setButtonLoading(submitButton, submitButton?.dataset.loadingText || 'Memproses...');
});

(function () {
    const modal = document.getElementById('globalConfirmModal');
    const title = document.getElementById('globalConfirmTitle');
    const message = document.getElementById('globalConfirmMessage');
    const ok = document.getElementById('globalConfirmOk');
    const cancel = document.getElementById('globalConfirmCancel');
    let pendingForm = null;

    function closeModal() {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
        pendingForm = null;
    }

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.confirmed === 'true') return;
        if (!form.dataset.confirmMessage) return;
        event.preventDefault();
        pendingForm = form;
        title.textContent = form.dataset.confirmTitle || 'Konfirmasi Aksi';
        message.textContent = form.dataset.confirmMessage;
        ok.textContent = form.dataset.confirmOk || 'Ya, Lanjutkan';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    ok?.addEventListener('click', function () {
        if (!pendingForm) return;
        pendingForm.dataset.confirmed = 'true';
        if (pendingForm.requestSubmit) {
            pendingForm.requestSubmit();
        } else {
            pendingForm.submit();
        }
    });
    cancel?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });
})();

document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        const inputs = document.querySelectorAll('[x-data*="searchBar"] input');
        for (const input of inputs) {
            if (input.offsetParent !== null) { input.focus(); return; }
        }
        if (inputs.length > 0) inputs[0].focus();
    }
});
