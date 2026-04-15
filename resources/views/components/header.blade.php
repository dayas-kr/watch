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

            <a href="{{ route('watchlist.index') }}" class="hidden lg:contents">
                <x-ui.button variant="ghost"
                    class="text-(--muted-foreground)! hover:text-(--foreground)! rounded-xl! h-7.5!">
                    <x-lucide-bookmark-plus /> Watchlist
                </x-ui.button>
            </a>

            <div x-data="{ open: false }" @keydown.escape.window="open = false" class="relative grid place-items-center">
                <button @click="open = !open" x-ref="profile"
                    class="size-8 rounded-full select-none bg-(--muted) overflow-hidden">
                    <img src="https://vercel.com/api/www/avatar?s=50&u=learntocodeon2023-6539" alt="avatar"
                        class="w-full h-full object-cover">
                </button>

                <div x-show="open" x-trap.noScroll="open" x-transition x-cloak
                    x-anchor.bottom-end.offset.6="$refs.profile" @click.outside="open = false"
                    class="bg-(--popover) rounded-xl p-1 border border-(--border) text-sm min-w-40 z-50 shadow-xs">

                    <a @mouseover="$el.focus()" href="{{ route('profile.edit') }}"
                        class="focus:bg-(--muted) focus:outline-none w-full px-2.5 py-1.25 flex items-center gap-2 rounded-lg text-(--secondary-foreground)">
                        <i class="fa-regular fa-user w-4 text-(--muted-foreground)"></i>
                        Profile
                    </a>

                    <a @mouseover="$el.focus()" href="{{ route('lists.index') }}"
                        class="focus:bg-(--muted) focus:outline-none w-full px-2.5 py-1.25 flex items-center gap-2 rounded-lg text-(--secondary-foreground)">
                        <i class="fa-solid fa-list w-4 text-(--muted-foreground)"></i>
                        Lists
                    </a>

                    <a @mouseover="$el.focus()" href="{{ route('watchlist.index') }}"
                        class="focus:bg-(--muted) focus:outline-none w-full px-2.5 py-1.25 flex items-center gap-2 rounded-lg text-(--secondary-foreground)">
                        <i class="fa-regular fa-bookmark w-4 text-(--muted-foreground)"></i>
                        Watchlist
                    </a>

                    <button @mouseover="$el.focus()"
                        class="focus:bg-(--muted) focus:outline-none w-full px-2.5 py-1.25 flex items-center gap-2 rounded-lg text-(--secondary-foreground)">
                        <i class="fa-solid fa-arrow-right-from-bracket w-4 text-(--muted-foreground)"></i>
                        Logout
                    </button>
                </div>
            </div>
        </nav>
    </div>
</header>
