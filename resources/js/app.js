import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
window.Swal = Swal;

/**
 * Rupiah Currency Formatting Helpers (Thousand separator with dots, pure numeric backend value, cursor preservation)
 */
window.sanitizeRupiahRaw = function(value) {
    if (value === null || value === undefined || value === '') return '';
    // If numeric or decimal string like "450000.00", parse integer value first
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

    // Count how many digits were to the left of the cursor before formatting
    const digitsBeforeCursor = oldVal.slice(0, start).replace(/\D/g, '').replace(/^0+/, '').length;

    const raw = oldVal.replace(/\D/g, '').replace(/^0+/, '');
    const formatted = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';

    inputEl.value = formatted;

    // Restore cursor position right after the same number of digits
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
 * Standard Viscode SweetAlert2 Confirmation Dialog
 */
window.confirmAction = function({
    title = 'Konfirmasi Tindakan',
    text = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
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
        customClass: {
            popup: 'rounded-2xl border border-slate-200 shadow-2xl p-6 font-sans',
            title: 'text-slate-900 font-bold text-lg',
            htmlContainer: 'text-slate-600 text-sm leading-relaxed',
            confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm shadow-sm cursor-pointer mx-1',
            cancelButton: 'px-4 py-2.5 rounded-xl font-semibold text-sm border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 cursor-pointer mx-1'
        },
        buttonsStyling: false
    });
};

/**
 * Form submit helper with SweetAlert2
 */
window.confirmFormSubmit = function(event, options = {}) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    
    window.confirmAction(options).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
};

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

Alpine.start();
