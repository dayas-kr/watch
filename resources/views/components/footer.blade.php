<footer class="border-t border-(--border) mt-8 bg-(--background)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <!-- Brand -->
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <x-lucide-clapperboard class="w-4 h-4 opacity-50" />
                    <span class="font-display text-[1.05rem] leading-none">Watch</span>
                </div>
                <p class="text-xs text-(--muted-foreground)">
                    Track movies & TV shows easily.
                </p>
            </div>

            <!-- Links -->
            <div class="flex items-center flex-wrap gap-x-4 gap-y-2 text-sm">
                <a href="/movies" class="text-(--foreground)/60 hover:text-(--foreground) font-medium">Movies</a>
                <a href="/tv" class="text-(--foreground)/60 hover:text-(--foreground) font-medium">TV Shows</a>
                <a href="/search" class="text-(--foreground)/60 hover:text-(--foreground) font-medium">Search</a>
                <a href="/watchlist" class="text-(--foreground)/60 hover:text-(--foreground) font-medium">Watchlist</a>
            </div>
        </div>

        <!-- Bottom -->
        <div
            class="mt-6 pt-4 border-t border-(--border)
            flex items-center justify-between gap-2
            text-xs text-(--muted-foreground)">
            <p>&copy; {{ date('Y') }} Watch</p>

            <p>
                Powered by
                <a href="https://www.themoviedb.org" target="_blank"
                    class="underline underline-offset-2 hover:text-(--foreground)">
                    TMDB API
                </a>
            </p>
        </div>
    </div>
</footer>
