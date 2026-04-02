<header class="h-14 border-b border-(--border) px-4 sm:px-6 bg-(--background)">
    <div class="flex items-center justify-between h-full max-w-7xl gap-4 mx-auto">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2.5 shrink-0">
            <x-lucide-clapperboard class="w-4 h-4 opacity-50" />
            <span class="font-display text-[1.1rem] leading-none">Watch</span>
        </a>

        <!-- Search (desktop) -->
        <div class="flex-1 max-w-2xl hidden md:block">
            <x-basic-search />
        </div>

        <!-- Nav -->
        <nav class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <!-- Mobile search icon -->
            <a href="/search"
                class="sm:hidden flex items-center justify-center w-8 h-8 rounded-xl text-(--muted-foreground) hover:text-(--foreground) hover:bg-(--muted) transition-colors">
                <x-lucide-search class="w-4 h-4" />
            </a>

            <x-ui.button href="/search" variant="ghost"
                class="text-(--muted-foreground)! hover:text-(--foreground)! rounded-xl! h-7.5!">
                <x-lucide-compass /> Discover
            </x-ui.button>
            <a href="{{ route('watchlist.index') }}" class="contents">
                <x-ui.button variant="ghost"
                    class="text-(--muted-foreground)! hover:text-(--foreground)! rounded-xl! h-7.5!">
                    <x-lucide-bookmark-plus /> Watchlist
                </x-ui.button>
            </a>
        </nav>
    </div>
</header>
