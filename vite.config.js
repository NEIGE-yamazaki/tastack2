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
        rollupOptions: {
            output: {
                manualChunks: {
                    capacitor: [
                        '@capacitor/core',
                        '@capacitor/app',
                        '@capacitor/browser',
                        '@capacitor/camera',
                        '@capacitor/device',
                        '@capacitor/geolocation',
                        '@capacitor/haptics',
                        '@capacitor/splash-screen',
                        '@capacitor/status-bar',
                        '@capacitor/toast'
                    ]
                }
            }
        },
        target: 'es2015',
        minify: 'terser'
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true
    }
});
