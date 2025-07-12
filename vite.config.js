import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/index.css',
                'resources/js/app.js',
                'resources/js/components/gallery.js',
                'resources/js/components/menu.js',
                'resources/js/components/newsletter.js',
                'resources/js/components/row-gallery.js',
                'resources/js/components/similar-projects.js',
                'resources/js/components/subinfo-simple.js',
                'resources/js/components/subinfo.js',
                'resources/js/components/termineToggle.js',
                'resources/js/helpers/filterProjects.js',
                'resources/js/helpers/insertTransparentVideo.js',
                'resources/js/pages/home.js',
                'resources/js/pages/project.js',
                'resources/js/pages/rows.js',
                'resources/js/pages/ueber.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
})