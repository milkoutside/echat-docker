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
            refresh: true,
        }),

        vue(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        // origin и HMR настраиваются через переменные окружения, чтобы корректно работать за обратным прокси
        origin: process.env.VITE_DEV_ORIGIN || undefined,
        hmr: {
            host: process.env.VITE_DEV_HMR_HOST || undefined,
            port: process.env.VITE_DEV_HMR_PORT ? Number(process.env.VITE_DEV_HMR_PORT) : undefined,
            protocol: (process.env.VITE_DEV_HMR_PROTOCOL as 'ws' | 'wss' | undefined) || undefined,
        },
    },
    resolve: {
        alias: {
            '@': path.join(__dirname, '/resources/ts'), // Исправленный алиас
            '~': path.join(__dirname, '/node_modules'),
        },
    },
    build: {
        chunkSizeWarningLimit: 3200, // Лимит размера чанка
    },
});
