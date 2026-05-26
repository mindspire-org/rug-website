import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { copyFileSync, mkdirSync, readdirSync, statSync } from 'fs';
import { join, extname } from 'path';

function copyAssetsPlugin() {
    return {
        name: 'copy-assets',
        closeBundle() {
            const src  = join(process.cwd(), 'resources/assets');
            const dest = join(process.cwd(), 'public/images');
            mkdirSync(dest, { recursive: true });
            try {
                readdirSync(src).forEach(file => {
                    const s = join(src, file);
                    if (statSync(s).isFile()) {
                        copyFileSync(s, join(dest, file.toLowerCase()));
                    }
                });
            } catch {}
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        copyAssetsPlugin(),
    ],
});
