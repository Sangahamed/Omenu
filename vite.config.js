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
        // Vite 8 n'embarque plus esbuild (le minifieur par défaut est Oxc) :
        // forcer 'esbuild' ici échouerait sur un paquet introuvable.
        rollupOptions: {
            output: {
                // Vite 8 (Rolldown) n'accepte plus la forme objet : manualChunks doit
                // être une fonction qui associe un id de module à un nom de chunk.
                manualChunks(id) {
                    if (/[\\/]node_modules[\\/](alpinejs|axios|chart\.js)[\\/]/.test(id)) {
                        return 'vendor';
                    }
                    if (/[\\/]node_modules[\\/](leaflet|leaflet\.markercluster)[\\/]/.test(id)) {
                        return 'leaflet';
                    }
                }
            }
        }
    }
});