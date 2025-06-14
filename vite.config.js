import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
        rollupOptions: {
            external: ['@mdi/font/css/materialdesignicons.min.css',
                      'material-design-icons-iconfont/dist/material-design-icons.css',
                      'bootstrap/dist/css/bootstrap.min.css',
                      'bootstrap/dist/js/bootstrap.bundle.min.js'],
        },
    },
});
