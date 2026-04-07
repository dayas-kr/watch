import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/pages/welcome.js",
                "resources/js/pages/watchlist.js",
                "resources/js/pages/movie.js",
                "resources/js/pages/tv.js",
            ],
            refresh: true,
        }),
    ],
});
