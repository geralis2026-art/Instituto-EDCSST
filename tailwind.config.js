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
                // Sitio público: Lexend para títulos, Source Sans 3 para texto
                display: ['Lexend', ...defaultTheme.fontFamily.sans],
                body: ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Identidad del instituto: navy institucional + dorado (solo acentos/CTA)
                marca: {
                    navy: '#0B1E4A',
                    'navy-claro': '#16306E',
                    dorado: '#D4A017',
                    'dorado-oscuro': '#8A6408',
                    'dorado-suave': '#FEF3C7',
                },
            },
        },
    },

    plugins: [forms],
};
