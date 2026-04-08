<x-base-layout title="Watch">
    <div class="flex flex-col min-h-screen font-body">
        <x-header />
        <main class="flex-1">
            <div class="max-w-7xl px-4 py-8 mx-auto sm:px-6 sm:py-10 space-y-10 md:space-y-14">
                <!-- Hero -->
                <section class="py-2 sm:py-4 text-center">
                    <p
                        class="text-[0.65rem] tracking-[0.18em] uppercase text-(--muted-foreground) mb-4 transition-all duration-500">
                        Your personal watch tracker
                    </p>

                    <h1
                        class="font-display text-5xl sm:text-6xl md:text-7xl leading-[1.1] tracking-tight text-(--foreground) mb-4">
                        What will you <em class="italic">watch next?</em>
                    </h1>

                    <p class="text-sm text-(--muted-foreground) leading-relaxed max-w-sm mx-auto mb-7">
                        Add titles to your watchlist, favorites & custom lists — plus get info, ratings and more.
                    </p>

                    <div class="flex items-center justify-center gap-3">
                        <x-ui.button href="/search" size="lg" class="rounded-full! pl-2.5 pr-4">
                            <x-lucide-compass /> Discover
                        </x-ui.button>
                        <x-ui.button href="#" size="lg" variant="ghost"
                            class="rounded-full! text-(--muted-foreground)! hover:text-(--foreground)! px-4!">
                            Browse trending <x-lucide-arrow-right class="w-3.5 h-3.5" />
                        </x-ui.button>
                    </div>
                </section>

                <!-- Featured (TV & Movies) -->
                <section x-data="featuredTabs" class="space-y-4 md:space-y-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">Featured</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Featured TV & Movies</h2>
                        </div>

                        <div @keydown.right.prevent="$focus.next(); $nextTick(() => document.activeElement.click())"
                            @keydown.left.prevent="$focus.previous(); $nextTick(() => document.activeElement.click())"
                            class="flex bg-(--card) border border-(--border) rounded-full p-0.5 w-fit shadow-xs">
                            <template x-for="(value, key) in tabs" :key="key">
                                <button @click="onTabChange(key)" :data-active="isTab(key)"
                                    :tabindex="isTab(key) ? 0 : -1" x-text="value"
                                    class="text-sm font-medium rounded-full text-(--muted-foreground) px-2.5 py-0.75 data-[active=true]:bg-(--muted) data-[active=true]:text-(--foreground)">
                                </button>
                            </template>
                        </div>
                    </div>

                    <template x-for="(label, key) in tabs" :key="key">
                        <div x-show="isTab(key)">
                            <!-- Skeleton -->
                            <div x-show="$data[key].loading && !$data[key].error"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 translate-y-6"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="flex gap-4 overflow-x-auto no-scrollbar">
                                <template x-for="i in 10" :key="i">
                                    <div class="shrink-0 flex flex-col space-y-2 w-48">
                                        <div class="bg-(--muted) rounded-xl aspect-2/3"></div>
                                        <div class="space-y-1">
                                            <div class="h-4.5 bg-(--muted) rounded"></div>
                                            <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Error -->
                            <div x-show="$data[key].error"
                                class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400">
                                <span>Something went wrong.</span>
                                <button @click="retry(key)" class="underline">Retry</button>
                            </div>

                            <!-- Empty -->
                            <div x-show="!$data[key].loading && !$data[key].error && !$data[key].results.length"
                                class="text-sm text-(--muted-foreground)">
                                No results found.
                            </div>

                            <!-- Content -->
                            <template x-if="!$data[key].loading && !$data[key].error && $data[key].results.length">
                                <x-ui.horizontal-slider x-show="isTab(key)"
                                    x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0 translate-y-6"
                                    x-transition:enter-end="opacity-100 translate-y-0">
                                    <template x-for="(title, index) in $data[key].results" :key="index">
                                        <x-titles.title-card />
                                    </template>
                                </x-ui.horizontal-slider>
                            </template>
                        </div>
                    </template>
                </section>

                <!-- Now Playing Movies -->
                <section x-data="nowPlayingMovies" class="space-y-4 md:space-y-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">Movies</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Now Playing</h2>
                        </div>
                        <x-ui.button href="/movies/now-playing" variant="ghost"
                            class="rounded-xl! text-(--muted-foreground)! hover:text-(--foreground)!">
                            View all
                        </x-ui.button>
                    </div>

                    <!-- Skeleton -->
                    <div x-show="loading && !error" class="flex gap-4 overflow-x-auto no-scrollbar">
                        <template x-for="i in 10" :key="i">
                            <div class="shrink-0 flex flex-col space-y-2 w-80">
                                <div class="bg-(--muted) rounded-xl aspect-video"></div>
                                <div class="space-y-1">
                                    <div class="h-4.5 bg-(--muted) rounded"></div>
                                    <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Error -->
                    <div x-show="error" class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400">
                        <span>Something went wrong.</span>
                        <button @click="retry" class="underline">Retry</button>
                    </div>

                    <!-- Empty -->
                    <div x-show="!loading && !error && !data.length" class="text-sm text-(--muted-foreground)">
                        No results found.
                    </div>

                    <!-- Content -->
                    <template x-if="!loading && !error && data.length">
                        <x-ui.horizontal-slider>
                            <template x-for="(movie, index) in data" :key="index">
                                <a :href="`/movie/${movie.id}`"
                                    class="shrink-0 flex flex-col space-y-2 w-80 group/card">
                                    <!-- Backdrop -->
                                    <div class="bg-(--muted) rounded-xl aspect-video relative overflow-hidden">
                                        <template x-if="movie.backdrop_path">
                                            <img :src="`https://image.tmdb.org/t/p/w500${movie.backdrop_path}`"
                                                :alt="movie.title"
                                                class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105">
                                        </template>

                                        <template x-if="!movie.backdrop_path">
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i
                                                    class="fa-regular fa-image text-4xl text-(--muted-foreground)/25"></i>
                                            </div>
                                        </template>

                                        <!-- Badge -->
                                        <div
                                            class="absolute top-2 left-2 flex items-center gap-1.5 bg-(--background)/80 backdrop-blur-sm rounded-md px-2 py-1">
                                            <i class="fa-solid fa-film text-[9px]"></i>
                                            <span class="text-[10px] font-semibold">In Theatres</span>
                                        </div>
                                    </div>

                                    <!-- Info -->
                                    <div class="space-y-0.5">
                                        <div x-text="movie.title"
                                            class="line-clamp-1 font-medium text-sm group-focus-visible/card:underline underline-offset-2">
                                        </div>

                                        <div class="flex items-center gap-1.5 text-xs text-(--muted-foreground)">
                                            <template x-if="movie.vote_average">
                                                <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
                                                <span x-text="movie.vote_average.toFixed(1)"
                                                    class="font-medium text-(--foreground)"></span>
                                                <span>·</span>
                                            </template>
                                            <span x-text="dayjs(movie.release_date).format('YYYY')"></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </x-ui.horizontal-slider>
                    </template>
                </section>

                <!-- Now Airing Today -->
                <section x-data="airingToday" class="space-y-4 md:space-y-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">TV Shows</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Airing Today</h2>
                        </div>
                        <x-ui.button href="/tv/airing-today" variant="ghost"
                            class="rounded-xl! text-(--muted-foreground)! hover:text-(--foreground)!">
                            View all
                        </x-ui.button>
                    </div>

                    <!-- Skeleton -->
                    <div x-show="loading && !error" class="flex gap-4 overflow-x-auto no-scrollbar">
                        <template x-for="i in 10" :key="i">
                            <div class="shrink-0 flex flex-col space-y-2 w-80">
                                <div class="bg-(--muted) rounded-xl aspect-video"></div>
                                <div class="space-y-1">
                                    <div class="h-4.5 bg-(--muted) rounded"></div>
                                    <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Error -->
                    <div x-show="error" class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400">
                        <span>Something went wrong.</span>
                        <button @click="retry" class="underline">Retry</button>
                    </div>

                    <!-- Empty -->
                    <div x-show="!loading && !error && !data.length" class="text-sm text-(--muted-foreground)">
                        No results found.
                    </div>

                    <!-- Content -->
                    <template x-if="!loading && !error && data.length">
                        <x-ui.horizontal-slider>
                            <template x-for="(show, index) in data" :key="index">
                                <a :href="`/tv/${show.id}`" class="shrink-0 flex flex-col space-y-2 w-80 group/card">
                                    <!-- Backdrop -->
                                    <div class="bg-(--muted) rounded-xl aspect-video relative overflow-hidden">
                                        <template x-if="show.backdrop_path">
                                            <img :src="`https://image.tmdb.org/t/p/w500${show.backdrop_path}`"
                                                :alt="show.name"
                                                class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105">
                                        </template>

                                        <template x-if="!show.backdrop_path">
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i
                                                    class="fa-regular fa-image text-4xl text-(--muted-foreground)/25"></i>
                                            </div>
                                        </template>

                                        <!-- Live badge -->
                                        <div
                                            class="absolute top-2 left-2 flex items-center gap-1.5 bg-(--background)/80 backdrop-blur-sm rounded-md px-2 py-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            <span class="text-[10px] font-semibold">Live now</span>
                                        </div>
                                    </div>

                                    <!-- Info -->
                                    <div class="space-y-0.5">
                                        <div x-text="show.name"
                                            class="line-clamp-1 font-medium text-sm group-focus-visible/card:underline underline-offset-2">
                                        </div>

                                        <div class="flex items-center gap-1.5 text-xs text-(--muted-foreground)">
                                            <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
                                            <span x-text="show.vote_average.toFixed(1)"
                                                class="font-medium text-(--foreground)"></span>
                                            <span>·</span>
                                            <span x-text="dayjs(show.first_air_date).format('YYYY')"></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </x-ui.horizontal-slider>
                    </template>
                </section>

                <!-- Trending (Today & This Week) -->
                <section x-data="trendingTabs" class="space-y-4 md:space-y-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">Trending</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">TV &amp; Movies</h2>
                        </div>

                        <div @keydown.right.prevent="$focus.next(); $nextTick(() => document.activeElement.click())"
                            @keydown.left.prevent="$focus.previous(); $nextTick(() => document.activeElement.click())"
                            class="flex bg-(--card) border border-(--border) rounded-full p-0.5 w-fit shadow-xs">
                            <template x-for="(value, key) in timeWindows" :key="key">
                                <button @click="onTabChange(key)" :data-active="isTab(key)"
                                    :tabindex="isTab(key) ? 0 : -1" x-text="value"
                                    class="text-sm font-medium rounded-full text-(--muted-foreground) px-2.5 py-0.75 data-[active=true]:bg-(--muted) data-[active=true]:text-(--foreground)">
                                </button>
                            </template>
                        </div>
                    </div>

                    <template x-for="(value, key) in timeWindows" :key="key">
                        <div x-show="isTab(key)">
                            <!-- Skeletons -->
                            <div x-show="$data[key].loading && !$data[key].error"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 translate-y-6"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="flex gap-4 overflow-x-auto no-scrollbar">
                                <template x-for="i in 10" :key="i">
                                    <div class="shrink-0 flex flex-col space-y-2 w-48">
                                        <div class="bg-(--muted) rounded-xl aspect-2/3"></div>
                                        <div class="space-y-1">
                                            <div class="h-4.5 bg-(--muted) rounded"></div>
                                            <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Error -->
                            <div x-show="$data[key].error"
                                class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400">
                                <span>Something went wrong.</span>
                                <button @click="retry(key)" class="underline">Retry</button>
                            </div>

                            <!-- Empty -->
                            <div x-show="!$data[key].loading && !$data[key].error && !$data[key].results.length"
                                class="text-sm text-(--muted-foreground)">
                                No results found.
                            </div>

                            <!-- Content -->
                            <template x-if="!$data[key].loading && !$data[key].error && $data[key].results.length">
                                <x-ui.horizontal-slider x-show="isTab(key)"
                                    x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0 translate-y-6"
                                    x-transition:enter-end="opacity-100 translate-y-0">
                                    <template x-for="(title, index) in $data[key].results" :key="index">
                                        <x-titles.title-card />
                                    </template>
                                </x-ui.horizontal-slider>
                            </template>
                        </div>
                    </template>
                </section>

                <!-- Popular (TV & Movies) -->
                <section x-data="popularTabs" class="space-y-4 md:space-y-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">Popular</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Right Now</h2>
                        </div>

                        <!-- Tabs -->
                        <div @keydown.right.prevent="$focus.next(); $nextTick(() => document.activeElement.click())"
                            @keydown.left.prevent="$focus.previous(); $nextTick(() => document.activeElement.click())"
                            class="flex bg-(--card) border border-(--border) rounded-full p-0.5 w-fit shadow-xs">
                            <template x-for="(label, key) in tabs" :key="key">
                                <button @click="onTabChange(key)" :data-active="isTab(key)"
                                    :tabindex="isTab(key) ? 0 : -1" x-text="label"
                                    class="text-sm font-medium rounded-full text-(--muted-foreground) px-2.5 py-0.75 data-[active=true]:bg-(--muted) data-[active=true]:text-(--foreground)">
                                </button>
                            </template>
                        </div>
                    </div>

                    <template x-for="(label, key) in tabs" :key="key">
                        <div x-show="isTab(key)">
                            <!-- Skeleton -->
                            <div x-show="$data[key].loading && !$data[key].error"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 translate-y-6"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="flex gap-4 overflow-x-auto no-scrollbar">
                                <template x-for="i in 10" :key="i">
                                    <div class="shrink-0 flex flex-col space-y-2 w-48">
                                        <div class="bg-(--muted) rounded-xl aspect-2/3"></div>
                                        <div class="space-y-1">
                                            <div class="h-4.5 bg-(--muted) rounded"></div>
                                            <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Error -->
                            <div x-show="$data[key].error"
                                class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400">
                                <span>Something went wrong.</span>
                                <button @click="retry(key)" class="underline">Retry</button>
                            </div>

                            <!-- Empty -->
                            <div x-show="!$data[key].loading && !$data[key].error && !$data[key].results.length"
                                class="text-sm text-(--muted-foreground)">
                                No results found.
                            </div>

                            <!-- Content -->
                            <template x-if="!$data[key].loading && !$data[key].error && $data[key].results.length">
                                <x-ui.horizontal-slider x-show="isTab(key)"
                                    x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0 translate-y-6"
                                    x-transition:enter-end="opacity-100 translate-y-0">
                                    <template x-for="(title, index) in $data[key].results" :key="index">
                                        <x-titles.title-card />
                                    </template>
                                </x-ui.horizontal-slider>
                            </template>

                        </div>
                    </template>
                </section>

                <!-- Top Rated (TV & Movies) -->
                <section x-data="topRatedList" class="space-y-4 md:space-y-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">All time</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Top Rated</h2>
                        </div>

                        <!-- Tabs -->
                        <div @keydown.right.prevent="$focus.next(); $nextTick(() => document.activeElement.click())"
                            @keydown.left.prevent="$focus.previous(); $nextTick(() => document.activeElement.click())"
                            class="flex bg-(--card) border border-(--border) rounded-full p-0.5 w-fit shadow-xs">

                            <template x-for="(label, key) in tabs" :key="key">
                                <button @click="setTab(key)" :data-active="isTab(key)"
                                    :tabindex="isTab(key) ? 0 : -1" x-text="label"
                                    class="text-sm font-medium rounded-full text-(--muted-foreground) px-2.5 py-0.75 data-[active=true]:bg-(--muted) data-[active=true]:text-(--foreground)">
                                </button>
                            </template>
                        </div>
                    </div>

                    <template x-for="(label, key) in tabs" :key="key">
                        <div x-show="isTab(key)">
                            <!-- Skeleton -->
                            <div x-show="$data[key].loading && !$data[key].error"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 translate-y-6"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="flex gap-4 overflow-x-auto no-scrollbar">
                                <template x-for="i in 10" :key="i">
                                    <div class="shrink-0 flex flex-col space-y-2 w-48">
                                        <div class="bg-(--muted) rounded-xl aspect-2/3"></div>
                                        <div class="space-y-1">
                                            <div class="h-4.5 bg-(--muted) rounded"></div>
                                            <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Error -->
                            <div x-show="$data[key].error" class="flex items-center gap-2 text-sm text-red-500">
                                <span>Something went wrong.</span>
                                <button @click="retry(key)" class="underline">Retry</button>
                            </div>

                            <!-- Empty -->
                            <div x-show="!$data[key].loading && !$data[key].error && !$data[key].data.length"
                                class="text-sm text-(--muted-foreground)">
                                No results found.
                            </div>

                            <!-- Content -->
                            <template x-if="!$data[key].loading && !$data[key].error && $data[key].data.length">
                                <x-ui.horizontal-slider x-show="isTab(key)"
                                    x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0 translate-y-6"
                                    x-transition:enter-end="opacity-100 translate-y-0">
                                    <template x-for="(title, index) in $data[key].data" :key="index">
                                        <x-titles.title-card />
                                    </template>
                                </x-ui.horizontal-slider>
                            </template>

                        </div>
                    </template>
                </section>

                <!-- Watchlist: Continue Watching (TV & Movies) -->
                @auth
                    <section x-data="watchlistTabs" class="space-y-4 md:space-y-6">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">Your list</p>
                                <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Continue Watching</h2>
                            </div>

                            <!-- Tabs -->
                            <div @keydown.right.prevent="$focus.next(); $nextTick(() => document.activeElement.click())"
                                @keydown.left.prevent="$focus.previous(); $nextTick(() => document.activeElement.click())"
                                class="flex bg-(--card) border border-(--border) rounded-full p-0.5 w-fit shadow-xs">
                                <template x-for="(label, key) in tabs" :key="key">
                                    <button @click="onTabChange(key)" :data-active="isTab(key)"
                                        :tabindex="isTab(key) ? 0 : -1" x-text="label"
                                        class="text-sm font-medium rounded-full text-(--muted-foreground) px-2.5 py-0.75 data-[active=true]:bg-(--muted) data-[active=true]:text-(--foreground)">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <template x-for="(label, key) in tabs" :key="key">
                            <div x-show="isTab(key)">
                                <!-- Skeleton -->
                                <div x-show="$data[key].loading && !$data[key].error"
                                    x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0 translate-y-6"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="flex gap-4 overflow-x-auto no-scrollbar">
                                    <template x-for="i in 10" :key="i">
                                        <div class="shrink-0 flex flex-col space-y-2 w-48">
                                            <div class="bg-(--muted) rounded-xl aspect-2/3"></div>
                                            <div class="space-y-1">
                                                <div class="h-4.5 bg-(--muted) rounded"></div>
                                                <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Error -->
                                <div x-show="$data[key].error"
                                    class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400">
                                    <span>Something went wrong.</span>
                                    <button @click="retry(key)" class="underline">Retry</button>
                                </div>

                                <!-- Empty -->
                                <div x-show="!$data[key].loading && !$data[key].error && !$data[key].results.length"
                                    class="text-sm text-(--muted-foreground)">
                                    No results found.
                                </div>

                                <!-- Content -->
                                <template x-if="!$data[key].loading && !$data[key].error && $data[key].results.length">
                                    <x-ui.horizontal-slider x-show="isTab(key)"
                                        x-transition:enter="transition ease-out duration-500"
                                        x-transition:enter-start="opacity-0 translate-y-6"
                                        x-transition:enter-end="opacity-100 translate-y-0">
                                        <template x-for="(title, index) in $data[key].results" :key="index">
                                            <x-titles.title-card />
                                        </template>
                                    </x-ui.horizontal-slider>
                                </template>
                            </div>
                        </template>
                    </section>
                @endauth

                <!-- On The Air -->
                <section x-data="onTheAir" class="space-y-4 md:space-y-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">TV Shows</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">On The Air</h2>
                        </div>
                        <x-ui.button href="/tv/on-the-air" variant="ghost"
                            class="rounded-xl! text-(--muted-foreground)! hover:text-(--foreground)!">
                            View all
                        </x-ui.button>
                    </div>

                    <!-- Skeleton -->
                    <div x-show="loading && !error" class="flex gap-4 overflow-x-auto no-scrollbar">
                        <template x-for="i in 10" :key="i">
                            <div class="shrink-0 flex flex-col space-y-2 w-48">
                                <div class="bg-(--muted) rounded-xl aspect-2/3"></div>
                                <div class="space-y-1">
                                    <div class="h-4.5 bg-(--muted) rounded"></div>
                                    <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Error -->
                    <div x-show="error" class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400">
                        <span>Something went wrong.</span>
                        <button @click="retry" class="underline">Retry</button>
                    </div>

                    <!-- Empty -->
                    <div x-show="!loading && !error && !data.length" class="text-sm text-(--muted-foreground)">
                        No results found.
                    </div>

                    <!-- Content -->
                    <template x-if="!loading && !error && data.length">
                        <x-ui.horizontal-slider>
                            <template x-for="(title, index) in data" :key="index">
                                {{-- <a :href="`/tv/${show.id}`" class="shrink-0 flex flex-col space-y-2 w-48 group/card">
                    <!-- Poster -->
                    <div class="bg-(--muted) rounded-xl aspect-2/3 relative overflow-hidden">
                        <template x-if="show.poster_path">
                            <img :src="`https://image.tmdb.org/t/p/w300${show.poster_path}`"
                                :alt="show.name"
                                class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105">
                        </template>

                        <template x-if="!show.poster_path">
                            <div class="w-full h-full flex items-center justify-center">
                                <i
                                    class="fa-regular fa-image text-4xl text-(--muted-foreground)/25"></i>
                            </div>
                        </template>

                        <!-- Airing badge -->
                        <div
                            class="absolute top-2 left-2 flex items-center gap-1.5 bg-(--background)/80 backdrop-blur-sm rounded-md px-1.5 py-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            <span class="text-[10px] font-semibold">Airing</span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="space-y-0.5">
                        <div x-text="show.name"
                            class="line-clamp-1 font-medium text-sm group-focus-visible/card:underline underline-offset-2">
                        </div>

                        <div class="flex items-center gap-1.5 text-xs text-(--muted-foreground)">
                            <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
                            <span x-text="show.vote_average.toFixed(1)"
                                class="font-medium text-(--foreground)"></span>
                            <span>·</span>
                            <span x-text="dayjs(show.first_air_date).format('YYYY')"></span>
                        </div>
                    </div>
                </a> --}}
                                <x-titles.title-card />
                            </template>
                        </x-ui.horizontal-slider>
                    </template>
                </section>

                <!-- Trending People -->
                <section x-data="trendingPeople" class="space-y-4 md:space-y-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[.18em] text-(--muted-foreground)">People</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Trending Faces</h2>
                        </div>
                        <x-ui.button href="/people" variant="ghost"
                            class="rounded-xl! text-(--muted-foreground)! hover:text-(--foreground)!">
                            View all
                        </x-ui.button>
                    </div>

                    <!-- Skeleton -->
                    <div x-show="loading && !error" class="flex gap-4 overflow-x-auto no-scrollbar">
                        <template x-for="i in 10" :key="i">
                            <div class="shrink-0 flex flex-col items-center space-y-2.5 w-28 text-center">
                                <div class="bg-(--muted) rounded-full w-20 h-20"></div>
                                <div class="space-y-1 w-full">
                                    <div class="h-4 bg-(--muted) rounded"></div>
                                    <div class="h-3 bg-(--muted) w-3/4 mx-auto rounded"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Error -->
                    <div x-show="error" class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400">
                        <span>Something went wrong.</span>
                        <button @click="retry" class="underline">Retry</button>
                    </div>

                    <!-- Empty -->
                    <div x-show="!loading && !error && !data.length" class="text-sm text-(--muted-foreground)">
                        No results found.
                    </div>

                    <!-- Content -->
                    <template x-if="!loading && !error && data.length">
                        <x-ui.horizontal-slider>
                            <template x-for="(person, index) in data" :key="index">
                                <a :href="`/person/${person.id}`"
                                    class="shrink-0 flex flex-col items-center space-y-2.5 w-28 text-center group/card">
                                    <!-- Avatar -->
                                    <div class="bg-(--muted) rounded-full size-24 overflow-hidden">
                                        <img :src="person.profile_path ?
                                            `https://image.tmdb.org/t/p/w185${person.profile_path}` :
                                            `https://ui-avatars.com/api/?name=${encodeURIComponent(person.name)}&background=random`"
                                            :alt="person.name" class="w-full h-full object-cover">
                                    </div>

                                    <!-- Info -->
                                    <div class="space-y-0.5 w-full">
                                        <div x-text="person.name"
                                            class="font-medium text-sm line-clamp-1 group-focus-visible/card:underline underline-offset-2">
                                        </div>

                                        <div class="text-xs text-(--muted-foreground) line-clamp-1"
                                            x-text="person.known_for_department">
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </x-ui.horizontal-slider>
                    </template>
                </section>
            </div>
        </main>
        <x-footer />
    </div>

    @push('head')
        @vite('resources/js/pages/welcome.js')
        <x-cdn.font-awesome />
        <x-cdn.dayjs />
    @endpush

    @push('styles')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600&display=swap');

            .font-display {
                font-family: 'DM Serif Display', serif;
            }
        </style>
    @endpush
</x-base-layout>
