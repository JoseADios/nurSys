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
                // Color principal (indigo)
                'primary': {
                    50: '#EEEEFE',
                    100: '#E7E7FF',
                    200: '#D1D2FF',
                    300: '#B9BAFF',
                    400: '#9293FF',
                    500: '#696CFF', // Color base principal
                    600: '#5F62E6',
                    700: '#5558CC',
                    800: '#4B4DB3',
                    900: '#41429A',
                },
                // Color secundario (gris azulado)
                'secondary': {
                    50: '#F8F9FA',
                    100: '#F1F1F5',
                    200: '#E9ECEF',
                    300: '#DEE2E6',
                    400: '#CED4DA',
                    500: '#8E9AAB', // Color base secundario
                    600: '#7D8A9A',
                    700: '#6C7989',
                    800: '#5E6974',
                    900: '#4A5461',
                },
                // Colores de estado
                'success': {
                    50: '#F0FDE9',
                    100: '#E1FBD2',
                    200: '#C8F8A6',
                    300: '#AAED7D',
                    400: '#8DE04F',
                    500: '#71DD37', // Verde
                    600: '#67C832',
                    700: '#57A82A',
                    800: '#488922',
                    900: '#3A6B1E',
                },
                'info': {
                    50: '#E6FCFD',
                    100: '#CCF9FA',
                    200: '#99F3F5',
                    300: '#66EDF0',
                    400: '#33E7EB',
                    500: '#05B5BA', // Turquesa
                    600: '#049AA0',
                    700: '#03858A',
                    800: '#03686D',
                    900: '#024B4E',
                },
                'warning': {
                    50: '#FFF8E6',
                    100: '#FFF1CC',
                    200: '#FFE399',
                    300: '#FFD666',
                    400: '#FFC933',
                    500: '#FFAB00', // Amarillo
                    600: '#E69A00',
                    700: '#CC8800',
                    800: '#B37700',
                    900: '#996600',
                },
                'danger': {
                    50: '#FFEBEC',
                    100: '#FED7D9',
                    200: '#FDB0B3',
                    300: '#FC888C',
                    400: '#FC6166',
                    500: '#FC4C51', // Rojo
                    600: '#E64449',
                    700: '#CC3C40',
                    800: '#B33538',
                    900: '#992D2F',
                },
            },
            boxShadow: {
                'btn': '0 2px 4px rgba(105, 108, 255, 0.25)',
                'btn-hover': '0 4px 8px rgba(105, 108, 255, 0.35)',
            },
            borderRadius: {
                'btn': '0.375rem',
            },
        },
    },

    plugins: [forms, typography],
};
