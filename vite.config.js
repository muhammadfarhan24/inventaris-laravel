import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
            buildDirectory: 'build'
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
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    server: {
        proxy: {
            '/barang': 'http://127.0.0.1:8000',
            '/kategori': 'http://127.0.0.1:8000',
            '/merk': 'http://127.0.0.1:8000',
            '/ruangan': 'http://127.0.0.1:8000',
        }
    },
    build: {
        outDir: 'public/build',
        manifest: true, 
        emptyOutDir: true
    }
});
