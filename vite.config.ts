import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/ts/app.ts'
            ],
            refresh: false, // Отключаем refresh для production
        }),

        vue(),
    ],
    resolve: {
        alias: {
            '@': path.join(__dirname, '/resources/ts'), // Исправленный алиас
            '~': path.join(__dirname, '/node_modules'),
        },
    },
    build: {
        chunkSizeWarningLimit: 3200, // Лимит размера чанка
        outDir: 'public/build', // Явно указываем директорию сборки
        assetsDir: 'assets', // Поддиректория для ассетов
        manifest: true, // Генерируем manifest.json для Laravel
        rollupOptions: {
            output: {
                manualChunks: undefined, // Отключаем разделение на чанки для простоты
            }
        }
    },
});
