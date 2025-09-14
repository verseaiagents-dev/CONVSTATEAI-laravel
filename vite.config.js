import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import autoprefixer from 'autoprefixer';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    css: {
        devSourcemap: true,
        postcss: {
            plugins: [
                autoprefixer({
                    overrideBrowserslist: ['> 1%', 'last 2 versions', 'not dead']
                }),
            ],
        },
        // CSS modules'ı kapatıyoruz, global CSS kullanıyoruz
        modules: false,
    },
    build: {
        cssCodeSplit: false, // CSS dosyalarını tek dosyada birleştir
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'assets/css/[name].[hash][extname]';
                    }
                    return 'assets/[name].[hash][extname]';
                },
                manualChunks: undefined, // Manuel chunk'ları kapat
            },
        },
        sourcemap: false, // Production'da source map kapalı
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
    },
});
