<x-base-layout title="Watchlist">
    <div class="flex flex-col min-h-screen font-body">
        <x-header />
        <main x-data="watchlist" class="flex-1">
            <div class="max-w-7xl px-4 py-8 mx-auto sm:px-3 sm:py-6">
                <div class="grid grid-cols-[1fr_auto_auto] items-center gap-x-3 gap-y-2 mb-6 md:mb-8">
                    <h1 class="text-xl sm:text-2xl font-semibold text-(--foreground)">
                        My Watchlist
                    </h1>

                    <!-- Media types tabs -->
                    <div @keydown.right.prevent="$focus.next(); $nextTick(() => document.activeElement.click())"
                        @keydown.left.prevent="$focus.previous(); $nextTick(() => document.activeElement.click())"
                        class="flex bg-(--card) border border-(--border) rounded-full p-0.5 w-fit shadow-xs">
                        <template x-for="(value, key) in sources" :key="key">
                            <button @click="onSourceChange(key)" :data-active="isSourceActive(key)"
                                :tabindex="isSourceActive(key) ? 0 : -1" x-text="value"
                                class="text-sm font-medium rounded-full text-(--muted-foreground) px-2.5 py-0.75 data-[active=true]:bg-(--muted) data-[active=true]:text-(--foreground) select-none">
                            </button>
                        </template>
                    </div>

                    <div class="flex gap-2 ml-auto col-span-full md:col-span-1">
                        <!-- Filter / Sort -->
                        <div class="relative">
                            <button @click="toggleFilterBy" x-ref="filterByTrigger" :aria-expanded="filterBy.open"
                                class="px-3 text-sm font-medium text-(--muted-foreground) hover:bg-(--muted)/75 active:bg-(--muted)/75 aria-expanded:bg-(--muted)/75 hover:text-(--foreground) rounded-xl h-7.5 select-none">
                                <span x-text="`Filter by: ${filterByLabel}`"></span>
                            </button>
                            <div x-show="filterBy.open" x-trap.noScroll="filterBy.open" @click.outside="closeFilterBy"
                                @keydown.escape.window="closeFilterBy" @keydown.down="$focus.next()"
                                @keydown.up="$focus.previous()" x-anchor.offset.4="$refs.filterByTrigger"
                                x-ref="filterByContent" x-transition
                                class="flex flex-col z-10 bg-(--popover) border border-(--border) shadow p-1 rounded-xl w-full">
                                <template x-for="(value, key) in filterBy.options" :key="key">
                                    <button :data-key="key" @click="filterBy.value = key; closeFilterBy()"
                                        @mouseenter="$el.focus()"
                                        class="flex px-3 py-1 select-none items-center text-sm font-medium focus:bg-(--muted) focus:outline-none text-left rounded-md">
                                        <span x-text="value"></span>
                                        <x-lucide-check class="size-3.5 ml-auto" stroke-width="3"
                                            x-show="filterBy.value === key" />
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Order by -->
                        <button @click="toggleOrderBy"
                            class="px-3 text-sm font-medium text-(--muted-foreground) hover:bg-(--muted)/75 active:bg-(--muted)/75 hover:text-(--foreground) rounded-xl h-7.5 select-none">
                            <span>Order by:</span>
                            <span x-text="$data[source].orderByCreatedAsc ? 'Newest' : 'Oldest'"></span>
                        </button>
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6 md:gap-8 gap-y-8">
                    <template x-for="(value, key) in sources" :key="key">
                        <template x-if="isSourceActive(key)">
                            <template x-for="item in getItems(key)" :key="item.id">
                                <a :href="`/${key}/${item.id}`"
                                    class="shrink-0 flex flex-col space-y-2 w-48 group/card">
                                    <!-- Poster -->
                                    <div class="bg-(--muted) rounded-xl aspect-2/3 relative overflow-hidden">
                                        <template x-if="item.poster_path">
                                            <img :src="`https://image.tmdb.org/t/p/w300${item.poster_path}`"
                                                :alt="item.title || item.name"
                                                class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105 z-1">
                                        </template>

                                        <div class="w-full h-full flex items-center justify-center z-0">
                                            <i class="fa-regular fa-image text-5xl text-(--muted-foreground)/25"></i>
                                        </div>
                                    </div>

                                    <!-- Info -->
                                    <div class="space-y-0.5">
                                        <div x-text="item.title || item.name"
                                            class="line-clamp-1 font-medium text-sm group-focus-visible/card:underline underline-offset-2">
                                        </div>

                                        <div class="flex items-center gap-1.5 text-xs text-(--muted-foreground)">
                                            <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
                                            <span x-text="item.vote_average.toFixed(1)"
                                                class="font-medium text-(--foreground)"></span>
                                            <span>·</span>
                                            <span x-text="formatDate(item.first_air_date || item.release_date)"></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </template>
                    </template>
                </div>

                <!-- Infinite scroll sentinel -->
                <div x-intersect.threshold.10="fetch(source)" class="col-span-full py-8 flex justify-center">
                    <template x-if="$data[source].loading">
                        <span class="text-sm text-(--muted-foreground) flex items-center gap-2">
                            <x-lucide-loader-circle class="size-5 animate-spin" />
                            Loading...
                        </span>
                    </template>

                    <template
                        x-if="$data[source].initialized && $data[source].data.length === 0 && !$data[source].loading">
                        <span class="text-sm text-(--muted-foreground)">
                            You haven't added any
                            <span x-text="source === 'movie' ? 'movies' : 'TV shows'"></span>
                            to your watchlist.
                        </span>
                    </template>

                    <template
                        x-if="$data[source].initialized && $data[source].data.length > 0 && !hasMore(source) && !$data[source].loading">
                        <span class="text-sm text-(--muted-foreground)">
                            You've reached the end.
                        </span>
                    </template>
                </div>
            </div>
        </main>
        <x-footer />
    </div>

    @push('head')
        @vite('resources/js/pages/watchlist.js')
        <x-cdn.font-awesome />
        <x-cdn.dayjs />
    @endpush

    @push('styles')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600&display=swap');

            .font-display {
                font-family: 'DM Serif Display', serif;
            }

            select {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                background: transparent !important;
            }

            select::-ms-expand {
                display: none;
            }
        </style>
    @endpush
</x-base-layout>
