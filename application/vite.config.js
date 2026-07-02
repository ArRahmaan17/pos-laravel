import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

const hostPort = Number(process.env.VITE_HOST_PORT ?? 5173);

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        origin: `http://localhost:${hostPort}`,
        cors: {
            origin: /^https?:\/\/(?:localhost|127\.0\.0\.1)(?::\d+)?$/,
        },
        hmr: {
            host: 'localhost',
            clientPort: hostPort,
        },
    },
    build: {
        manifest: 'manifest.json',
    },
});
