import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Evita que esbuild minifique los media queries con la sintaxis
        // de rango (`@media (width>=1024px)`), que navegadores más
        // antiguos no soportan y hace que ignoren todo el bloque `lg:`,
        // `md:`, etc. — rompiendo el layout responsive por completo.
        cssTarget: ['chrome100', 'firefox100', 'safari15', 'edge100'],
    },
});
