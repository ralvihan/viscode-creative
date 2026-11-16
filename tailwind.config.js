import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    navy: '#17243B',
                    'navy-dark': '#101B2E',
                    'navy-light': '#1E2E4A',
                    blue: '#2563EB',
                    'blue-hover': '#1D4ED8',
                    cyan: '#22D3EE',
                    charcoal: '#172033',
                    muted: '#64748B',
                    surface: '#F3F5F9',
                    light: '#F8FAFC',
                    border: '#E2E8F0',
                },
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03)',
                'card-hover': '0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04)',
                'modal': '0 20px 25px -5px rgba(15, 23, 42, 0.15), 0 10px 10px -5px rgba(15, 23, 42, 0.05)',
            },
        },
    },

    plugins: [forms],
};
