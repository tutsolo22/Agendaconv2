import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/Modules/Facturacion/cfdi40/cfdi.js', 'resources/js/Modules/Facturacion/retenciones/retenciones.js'],
            refresh: true,
        }),
    ],
});
