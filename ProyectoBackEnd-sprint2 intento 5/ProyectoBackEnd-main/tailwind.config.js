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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Paleta institucional UCSC (burdeo)
                ucsc: {
                    50: '#faf4f4',
                    100: '#f3e6e7',
                    200: '#e7cbce',
                    300: '#d5a4aa',
                    400: '#bd757e',
                    500: '#a1505b',
                    600: '#8a3f4a',
                    700: '#72333d',
                    800: '#5e2e36',
                    900: '#4f2a31',
                },
            },
        },
    },

    plugins: [forms],
};
