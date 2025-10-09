import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import Components from 'unplugin-vue-components/vite';
import { AntDesignVueResolver } from 'unplugin-vue-components/resolvers';

export default defineConfig({
    define: {
        'process.env': {
            VITE_APP_URL: process.env.VITE_APP_URL || 'http://my-focus-news:80',
        },
    },
    server: { 
        hmr: {
            host: 'localhost',
        },
    },     
    plugins: [
        vue(),        
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        Components({
            resolvers: [
                AntDesignVueResolver({
                    importStyle: false, // css in js
                }),
            ],
        }),
    ],
});
