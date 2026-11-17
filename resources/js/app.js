import Alpine from 'alpinejs';
import jQuery from 'jquery';
import select2Factory from 'select2/dist/js/select2.full.js';
import 'select2/dist/css/select2.min.css';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.$ = window.jQuery = jQuery;
select2Factory(window, jQuery);

window.Alpine = Alpine;
window.Swal = Swal;

/**
 * Rupiah Currency Formatting Helpers (Thousand separator with dots, pure numeric backend value, cursor preservation)
 */
window.sanitizeRupiahRaw = function(value) {
    if (value === null || value === undefined || value === '') return '';
    if (typeof value === 'number') {
        return value > 0 ? String(Math.round(value)) : '';
    }
    const str = String(value).trim();
    if (/^\d+\.\d{2}$/.test(str)) {
        const num = Math.round(parseFloat(str));
        return num > 0 ? String(num) : '';
    }
    const digits = str.replace(/\D/g, '').replace(/^0+/, '');
    return digits;
};

window.formatRupiahDots = function(value) {
    const raw = window.sanitizeRupiahRaw(value);
    if (!raw) return '';
    return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

window.formatRupiahInputWithCursor = function(inputEl) {
    const start = inputEl.selectionStart || 0;
    const oldVal = inputEl.value;

    const digitsBeforeCursor = oldVal.slice(0, start).replace(/\D/g, '').replace(/^0+/, '').length;

    const raw = oldVal.replace(/\D/g, '').replace(/^0+/, '');
    const formatted = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';

    inputEl.value = formatted;

    if (document.activeElement === inputEl) {
        let newCursorPos = 0;
        let seenDigits = 0;
        for (let i = 0; i < formatted.length; i++) {
            if (seenDigits >= digitsBeforeCursor) break;
            if (/\d/.test(formatted[i])) {
                seenDigits++;
            }
            newCursorPos = i + 1;
        }
        inputEl.setSelectionRange(newCursorPos, newCursorPos);
    }

    return raw;
};

/**
 * Initialize Select2 on all <select> elements & bridge with Alpine.js (x-model / @change)
 */
window.initViscodeSelect2 = function(root = document) {
    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) return;

    const $ = window.jQuery;
    const selects = $(root).find('select:not([data-no-select2])');

    selects.each(function() {
        const selectEl = this;
        const $select = $(selectEl);

        // If already initialized, just sync its current value with Select2 UI
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.trigger('change.select2');
            return;
        }

        const firstOptText = $select.find('option:first').text().trim();
        const optionCount = $select.find('option').length;

        $select.select2({
            width: '100%',
            dropdownParent: $(document.body),
            minimumResultsForSearch: optionCount > 4 ? 0 : 6,
            placeholder: firstOptText.startsWith('--') ? firstOptText : undefined,
            language: {
                noResults: function() {
                    return 'Pilihan tidak ditemukan...';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });

        // Bridge Select2 selection -> Native DOM events for Alpine.js (x-model, @change)
        $select.on('select2:select select2:clear', function() {
            if (selectEl._vcSyncing) return;
            selectEl._vcSyncing = true;
            selectEl.dispatchEvent(new Event('input', { bubbles: true }));
            selectEl.dispatchEvent(new Event('change', { bubbles: true }));
            setTimeout(() => {
                selectEl._vcSyncing = false;
            }, 10);
        });
    });
};

/**
 * Resolve human-readable field label inside a form
 */
function getFieldLabelText(el) {
    const wrapper = el.closest('div');
    if (wrapper) {
        const lbl = wrapper.querySelector('label');
        if (lbl) {
            return lbl.textContent.replace('*', '').trim();
        }
        const parentWrapper = wrapper.parentElement ? wrapper.parentElement.closest('div') : null;
        if (parentWrapper) {
            const parentLbl = parentWrapper.querySelector('label');
            if (parentLbl) {
                return parentLbl.textContent.replace('*', '').trim();
            }
        }
    }
    return el.getAttribute('placeholder') || el.getAttribute('name') || 'Kolom wajib';
}

/**
 * Validate form required fields & constraints using SweetAlert2
 */
window.validateFormWithSwal = function(form) {
    if (!form || !(form instanceof HTMLFormElement)) return true;

    // Disable native browser tooltip bubble so SweetAlert2 handles it cleanly
    form.setAttribute('novalidate', 'novalidate');

    const elements = Array.from(form.querySelectorAll('input, select, textarea'));

    for (const el of elements) {
        if (el.disabled) continue;

        // Skip hidden inputs UNLESS it is a hidden Rupiah numeric backing input whose visible input is required
        if (el.type === 'hidden') continue;

        // Skip elements hidden by x-show inside the form (e.g. inactive createMode tab)
        if (el.offsetParent === null && !el.classList.contains('select2-hidden-accessible')) {
            continue;
        }

        const labelText = getFieldLabelText(el);
        const val = (el.value || '').trim();

        if (el.hasAttribute('required') && val === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                html: `Kolom <b>${labelText}</b> wajib diisi sebelum melanjutkan.`,
                confirmButtonText: 'Isi Sekarang',
                confirmButtonColor: '#2563EB',
                showClass: { popup: 'vc-swal-show' },
                customClass: {
                    popup: 'rounded-2xl border border-slate-200 shadow-2xl p-6 font-sans',
                    title: 'text-slate-900 font-bold text-lg',
                    htmlContainer: 'text-slate-600 text-sm leading-relaxed',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm bg-blue-600 hover:bg-blue-700 text-white shadow-sm cursor-pointer'
                },
                buttonsStyling: false
            }).then(() => {
                if (window.jQuery && window.jQuery(el).hasClass('select2-hidden-accessible')) {
                    window.jQuery(el).select2('open');
                } else {
                    el.focus();
                }
            });
            return false;
        }

        if (el.type === 'email' && val !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            Swal.fire({
                icon: 'warning',
                title: 'Format Email Tidak Valid',
                html: `Mohon masukkan alamat email yang benar pada kolom <b>${labelText}</b>.`,
                confirmButtonText: 'Perbaiki Email',
                confirmButtonColor: '#2563EB',
                showClass: { popup: 'vc-swal-show' },
                customClass: {
                    popup: 'rounded-2xl border border-slate-200 shadow-2xl p-6 font-sans',
                    title: 'text-slate-900 font-bold text-lg',
                    htmlContainer: 'text-slate-600 text-sm leading-relaxed',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm bg-blue-600 hover:bg-blue-700 text-white shadow-sm cursor-pointer'
                },
                buttonsStyling: false
            }).then(() => el.focus());
            return false;
        }
    }

    return true;
};

/**
 * Standard Viscode SweetAlert2 Confirmation Dialog
 */
window.confirmAction = function({
    title = 'Konfirmasi Tindakan',
    text = 'Apakah Anda yakin ingin menyimpan dan melanjutkan proses ini?',
    html = null,
    icon = 'question',
    confirmButtonText = 'Ya, Lanjutkan',
    cancelButtonText = 'Batal',
    confirmButtonColor = '#2563EB',
    isDanger = false
} = {}) {
    return Swal.fire({
        title: title,
        text: html ? undefined : text,
        html: html || undefined,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        confirmButtonColor: isDanger ? '#DC2626' : confirmButtonColor,
        cancelButtonColor: '#64748B',
        reverseButtons: true,
        showClass: { popup: 'vc-swal-show' },
        customClass: {
            popup: 'rounded-2xl border border-slate-200 shadow-2xl p-6 font-sans',
            title: 'text-slate-900 font-bold text-lg',
            htmlContainer: 'text-slate-600 text-sm leading-relaxed',
            confirmButton: `px-5 py-2.5 rounded-xl font-semibold text-sm text-white shadow-sm cursor-pointer mx-1 ${isDanger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-blue-600 hover:bg-blue-700'}`,
            cancelButton: 'px-4 py-2.5 rounded-xl font-semibold text-sm border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 cursor-pointer mx-1'
        },
        buttonsStyling: false
    });
};

/**
 * Helper to preserve submitter button name/value and submit form cleanly
 */
function submitFormSafely(form, submitter = null) {
    form._vcSwalConfirmed = true;
    if (submitter && submitter.name && !form.querySelector(`input[type="hidden"][name="${submitter.name}"]`)) {
        const tempInput = document.createElement('input');
        tempInput.type = 'hidden';
        tempInput.name = submitter.name;
        tempInput.value = submitter.value;
        form.appendChild(tempInput);
    }
    form.submit();
}

/**
 * Form submit helper with SweetAlert2 Validation + Confirmation
 */
window.confirmFormSubmit = function(event, options = {}) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    const submitter = event.submitter || null;

    if (form._vcSwalConfirmed) {
        return true;
    }

    if (!window.validateFormWithSwal(form)) {
        return false;
    }

    window.confirmAction(options).then((result) => {
        if (result.isConfirmed) {
            submitFormSafely(form, submitter);
        }
    });
    return false;
};

/**
 * Global Interceptor: Ensure EVERY POST/PUT/DELETE form in Viscode BMS
 * runs SweetAlert2 validation AND confirmation before submitting!
 */
document.addEventListener('submit', function(event) {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    // Skip GET filter forms or forms explicitly marked with data-no-swal
    const method = (form.getAttribute('method') || 'GET').toUpperCase();
    if (method === 'GET' || form.hasAttribute('data-no-swal') || form._vcSwalConfirmed) {
        return;
    }

    // Skip login/logout forms so authentication stays instant
    const action = form.getAttribute('action') || '';
    if (action.endsWith('/login') || action.endsWith('/logout')) {
        return;
    }

    // If the form already has an inline onsubmit calling confirmFormSubmit, let that run
    const inlineOnsubmit = form.getAttribute('onsubmit') || '';
    if (inlineOnsubmit.includes('confirmFormSubmit')) {
        return;
    }

    event.preventDefault();
    const submitter = event.submitter || form.querySelector('button[type="submit"]');

    // 1. Validate required fields with SweetAlert2 first
    if (!window.validateFormWithSwal(form)) {
        return;
    }

    // 2. Build contextual confirmation dialog from modal title or button label
    const modalBox = form.closest('[data-viscode-modal-box]');
    const modalTitle = modalBox ? modalBox.getAttribute('data-modal-title') : null;
    const btnText = submitter ? submitter.textContent.trim() : 'Simpan Data';
    const isDeleteOrReject = btnText.toLowerCase().includes('hapus') ||
        btnText.toLowerCase().includes('tolak') ||
        btnText.toLowerCase().includes('batalkan') ||
        btnText.toLowerCase().includes('lost');

    window.confirmAction({
        title: modalTitle ? `${modalTitle}?` : `${btnText}?`,
        html: `Apakah Anda yakin ingin menjalankan tindakan <b>${btnText}</b>?<br><span class="text-xs text-slate-500">Pastikan seluruh data yang dimasukkan sudah benar.</span>`,
        icon: isDeleteOrReject ? 'warning' : 'question',
        confirmButtonText: `Ya, ${btnText}`,
        cancelButtonText: 'Periksa Kembali',
        isDanger: isDeleteOrReject
    }).then((result) => {
        if (result.isConfirmed) {
            submitFormSafely(form, submitter);
        }
    });
});

/**
 * Toast Notification Helper
 */
window.showToast = function(message, icon = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: icon,
        title: message
    });
};

// Initialize Select2 on DOM ready and whenever a modal opens
document.addEventListener('DOMContentLoaded', () => {
    window.initViscodeSelect2(document);
});

window.addEventListener('viscode-modal-opened', (e) => {
    const modalEl = e.detail && e.detail.modal ? e.detail.modal : document;
    window.initViscodeSelect2(modalEl);
});

window.addEventListener('viscode-modal-closed', () => {
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
        window.jQuery('select.select2-hidden-accessible').select2('close');
    }
});

Alpine.start();
