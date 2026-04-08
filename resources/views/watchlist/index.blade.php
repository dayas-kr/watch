<x-base-layout title="Watchlist">
    <div class="flex flex-col min-h-screen font-body">
        <x-header />
        <main x-data="watchlist" @delete:soft.window="softDelete($event)"
            @delete:rollback.window="rollbackDelete($event)" @delete:permanent.window="delete($event)" class="flex-1">
            <div class="max-w-7xl px-4 py-8 mx-auto sm:px-3 sm:py-6">
                <div class="grid grid-cols-[1fr_auto_auto] items-center gap-x-3 gap-y-2 mb-4 md:mb-6 lg:mb-8">
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

                    <div class="flex items-center gap-2 ml-auto col-span-full md:col-span-1">
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
                            class="text-sm font-medium text-(--muted-foreground) hover:bg-(--muted)/75 active:bg-(--muted)/75 hover:text-(--foreground) rounded-full size-7.5 select-none grid place-items-center">
                            <i class="fa-solid fa-arrow-down"
                                :class="{ 'rotate-180': $data[source].orderByCreatedAsc }"></i>
                        </button>

                        <!-- View mode toggle -->
                        <div @keydown.right.prevent="$focus.next(); $nextTick(() => document.activeElement.click())"
                            @keydown.left.prevent="$focus.previous(); $nextTick(() => document.activeElement.click())"
                            class="flex bg-(--card) border border-(--border) rounded-xl p-0.5 shadow-xs">
                            <!-- Grid view -->
                            <button @click="gridView = true" :data-active="gridView" title="Grid view"
                                class="flex items-center justify-center w-7 h-6.5 rounded-lg text-(--muted-foreground) data-[active=true]:bg-(--muted) data-[active=true]:text-(--foreground) hover:text-(--foreground) transition-colors">
                                <x-lucide-layout-grid class="size-3.5" />
                            </button>
                            <!-- List view -->
                            <button @click="gridView = false" :data-active="!gridView" title="List view"
                                class="flex items-center justify-center w-7 h-6.5 rounded-lg text-(--muted-foreground) data-[active=true]:bg-(--muted) data-[active=true]:text-(--foreground) hover:text-(--foreground) transition-colors">
                                <x-lucide-list class="size-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <template x-for="(value, key) in sources" :key="key">
                    <template x-if="isSourceActive(key)">
                        <div>
                            <div x-show="gridView"
                                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6 md:gap-8 gap-y-8">
                                <template x-for="title in getItems(key)" :key="title.id">
                                    <div class="shrink-0 flex flex-col space-y-2 group/card">
                                        <!-- Poster -->
                                        <a :href="`/${title.media_type}/${title.id}`"
                                            class="bg-(--muted) rounded-xl aspect-2/3 relative overflow-hidden group/img">
                                            <template x-if="title.poster_path">
                                                <img :src="`https://image.tmdb.org/t/p/w300${title.poster_path}`"
                                                    :alt="title.title || title.name"
                                                    class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105 z-1">
                                            </template>

                                            <div class="w-full h-full flex items-center justify-center z-0">
                                                <i
                                                    class="fa-regular fa-image text-5xl text-(--muted-foreground)/25"></i>
                                            </div>

                                            <!-- Actions -->
                                            <div @click.prevent
                                                class="z-10 bg-neutral-500/50 absolute bottom-0 left-0 right-0 backdrop-blur-sm group-hover/img:translate-y-0 flex flex-col gap-2 p-2 translate-y-full transition-all cursor-default">
                                                <button
                                                    @click.prevent="$dispatch('watchlist:remove',  { media_id: title.id, media_type: title.media_type, page: $store.db.route })"
                                                    class="h-7 w-full border border-transparent bg-neutral-500 hover:bg-red-400 rounded-full flex items-center justify-center gap-2 text-white group/delete text-sm font-medium focus:outline-none">
                                                    <x-lucide-trash class="size-4" stroke-width="2.5" />
                                                    Remove
                                                </button>
                                            </div>
                                        </a>

                                        <!-- Info -->
                                        <div class="space-y-0.5">
                                            <a :href="`/${title.media_type}/${title.id}`"
                                                x-text="title.title || title.name"
                                                class="line-clamp-1 font-medium text-sm hover:underline underline-offset-2">
                                            </a>

                                            <div class="flex items-center gap-1.5 text-sm text-(--muted-foreground)">
                                                <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
                                                <span x-text="title.vote_average.toFixed(1)" class="font-medium"></span>
                                                <template x-if="title.vote_count">
                                                    <span x-text="`(${formatNumeral(title.vote_count)})`"
                                                        class="font-medium uppercase"></span>
                                                </template>
                                                <span>·</span>
                                                <span
                                                    x-text="formatDate(title.first_air_date || title.release_date)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="!gridView" class="grid">
                                <template x-for="(item, index) in getItems(key)" :key="item.id">
                                    <div class="flex flex-col py-2"
                                        :class="{ 'border-t border-(--border)': index !== 0 }">
                                        <div class="flex items-center gap-4">
                                            <a :href="`/${item.media_type}/${item.id}`"
                                                class="w-20 aspect-2/3 bg-(--muted) rounded-md relative overflow-hidden shrink-0 mb-auto">
                                                <template x-if="item.poster_path">
                                                    <img :src="`https://image.tmdb.org/t/p/w300${item.poster_path}`"
                                                        :alt="item.title || item.name"
                                                        class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105 z-1">
                                                </template>

                                                <div class="w-full h-full flex items-center justify-center z-0">
                                                    <i
                                                        class="fa-regular fa-image text-5xl text-(--muted-foreground)/25"></i>
                                                </div>
                                            </a>
                                            <div class="space-y-0.5">
                                                <a :href="`/${item.media_type}/${item.id}`"
                                                    x-text="item.title || item.name"
                                                    class="font-medium hover:underline underline-offset-2"></a>
                                                <div
                                                    class="flex flex-row items-center gap-y-0.5 gap-x-2 md:flex-col md:items-start">
                                                    <div
                                                        class="flex items-center gap-1.5 text-sm text-(--muted-foreground)">
                                                        <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
                                                        <span x-text="item.vote_average.toFixed(1)"
                                                            class="font-medium"></span>
                                                        <template x-if="item.vote_count">
                                                            <span x-text="`(${formatNumeral(item.vote_count)})`"
                                                                class="font-medium uppercase"></span>
                                                        </template>
                                                    </div>
                                                    <span
                                                        class="md:hidden font-extrabold text-(--muted-foreground)">&middot;</span>
                                                    <div x-text="formatDate(item.first_air_date || item.release_date)"
                                                        class="text-sm text-(--muted-foreground) font-medium md:order-first">
                                                    </div>
                                                </div>

                                                <div x-data="{
                                                    expanded: false,
                                                    limit: 200,
                                                    text: item.overview,
                                                    get shortText() {
                                                        return this.text.length > this.limit ?
                                                            this.text.slice(0, this.limit) + '...' :
                                                            this.text;
                                                    }
                                                }"
                                                    class="text-sm text-(--muted-foreground)">
                                                    <!-- Text -->
                                                    <p x-text="expanded ? text : shortText"></p>

                                                    <!-- Only show until expanded -->
                                                    <button x-show="!expanded && text.length > limit"
                                                        @click="expanded = true"
                                                        class="underline text-(--foreground) underline-offset-2 font-medium">
                                                        Read more
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </template>

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

    <x-titles.watchlist-manager />

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
