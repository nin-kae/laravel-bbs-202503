import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css:{
        preprocessors: {
            scss: {
                quietDeps: true // 隐藏以来项警告
            }
        }
    }
});
