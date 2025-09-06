import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import wasm from 'vite-plugin-wasm';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        wasm(),
        vue(),
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
