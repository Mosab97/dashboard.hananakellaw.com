import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/form-builder/style.css",
                "resources/js/app.js",
                "resources/js/form-builder/form-builder.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
            "~": "/resources/js/form-builder",
            "jquery-ui": path.resolve(
                __dirname,
                "node_modules/jquery-ui-dist/jquery-ui.js"
            ),
            // Add this new alias
            "~jquery-ui": path.resolve(
                __dirname,
                "node_modules/jquery-ui-dist"
            ),
        },
    },
    optimizeDeps: {
        include: ["jquery", "jquery-ui"],
    },
});
