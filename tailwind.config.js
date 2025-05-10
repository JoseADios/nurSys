import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary': '#696CFF',
                'primary-hover': '#5d60ff',
                'primary-active': '#4f52ff',
                // Puedes agregar más colores personalizados
                'secondary': '#8592A3',
                'success': '#71DD37',
                'danger': '#FF3E1D',
                'warning': '#FFAB00',
                'info': '#03C3EC',
            }
        },
    },

    plugins: [forms, typography],
};
