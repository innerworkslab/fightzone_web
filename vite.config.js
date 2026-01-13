import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import Components from "unplugin-vue-components/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/ts/app.ts"],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
        Components({
            dirs: ["resources/views/components"],
            extensions: ["vue"],
            deep: true,
            dts: "resources/js/ts/components.d.ts",
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources/js/ts",
        },
    },
    server: {
        proxy: {
            "/api": {
                target: "http://localhost:8000",
                changeOrigin: true,
            },
        },
    },
});
