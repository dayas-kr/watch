<div {{ $attributes->merge(['class' => 'flex flex-col gap-3']) }}>
    @auth
        {{-- <div x-data="watchlistButton" class="flex w-full" @keydown.escape.window="open = false"
            @keydown.tab.prevent="open = false" @keydown.arrow-down.prevent="navigate(1)"
            @keydown.arrow-up.prevent="navigate(-1)" @keydown.enter.prevent="select()">
            <button
                @click="$dispatch(`watchlist:${inWatchlist ? 'remove' : 'add'}`, { media_id: $store.title.id, media_type: $store.title.media_type })"
                class="bg-(--primary) text-(--primary-foreground) border-(--primary) shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] hover:opacity-90 focus:opacity-90 flex items-center justify-center gap-2 border px-4 py-2 rounded-l-full text-sm font-medium transition-all duration-200 outline-none select-none cursor-pointer flex-1 h-9.5">
                <i class="fa-bookmark" :class="inWatchlist ? 'fa-solid' : 'fa-regular'"></i>
                <span x-text="inWatchlist ? 'Remove from Watchlist' : 'Add to Watchlist'"></span>
            </button>

            <div
                class="bg-(--primary) border-(--primary) w-px self-stretch border-y shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] h-9.5">
                <div class="bg-(--primary-foreground) h-full w-full opacity-20"></div>
            </div>

            <button x-ref="listBtn" @click="openDropdown()"
                class="bg-(--primary) text-(--primary-foreground) border-(--primary) shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] hover:opacity-90 focus:opacity-90 flex items-center justify-center border px-3 py-2 rounded-r-full text-sm transition-all duration-200 outline-none select-none cursor-pointer">
                <i class="fa-solid fa-chevron-down text-xs" :class="open && 'rotate-180'"
                    style="transition: rotate 200ms"></i>
            </button>

            <div x-show="open" x-trap.noScroll="open" x-transition x-anchor.bottom-end.offset.6="$refs.listBtn"
                @click.outside="open = false"
                class="bg-(--popover) border border-(--border) p-2 rounded-xl shadow-xs min-w-3xs z-50 flex flex-col gap-1">
                <div class="flex items-center gap-2 mb-1">
                    <x-ui.input-group>
                        <x-ui.input-group-addon>
                            <x-lucide-search />
                        </x-ui.input-group-addon>
                        <x-ui.input-group-input x-ref="searchInput" x-model="search" placeholder="Search lists..."
                            @input="onSearchInput()" @click="onSearchClick($event)" />
                    </x-ui.input-group>

                    <x-ui.button @click="$dispatch('list:open-create-dialog')" variant="outline" size="icon"
                        title="Create a new list">
                        <x-lucide-plus />
                    </x-ui.button>
                </div>

                <div x-ref="listContainer" class="flex flex-col gap-1 mt-1 max-h-48 overflow-y-auto">

                    <!-- Loading -->
                    <template x-if="loading">
                        <div class="flex items-center justify-center gap-2 py-1 text-center text-sm">
                            <x-lucide-loader-2 class="animate-spin size-3" />
                            <span>Loading...</span>
                        </div>
                    </template>

                    <!-- Error -->
                    <template x-if="!loading && error">
                        <div class="flex flex-col items-center gap-2 py-3">
                            <p class="text-(--destructive) text-xs text-center" x-text="errorMessage"></p>
                            <button @click="fetch()"
                                class="text-(--muted-foreground) hover:text-(--foreground) text-xs underline cursor-pointer transition-colors">
                                Try again
                            </button>
                        </div>
                    </template>

                    <!-- Lists -->
                    <template x-if="!loading && !error">
                        <div class="flex flex-col gap-1 max-h-48 overflow-y-auto no-scrollbar">
                            <template x-for="(list, index) in filtered" :key="list.id">
                                <button :data-index="index" @click="activeIndex = index; select()"
                                    @mouseenter="activeIndex = index" :data-active="activeIndex === index"
                                    class="data-[active=true]:bg-(--muted) flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-left transition-colors cursor-pointer w-full">
                                    <span x-text="list.name"></span>
                                </button>
                            </template>

                            <p x-show="initialized && filtered.length === 0"
                                class="text-(--muted-foreground) text-xs text-center py-2">
                                No lists found
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </div> --}}

        {{-- ! --}}
        <div class="flex w-full">
            <button
                @click="$dispatch(`watchlist:${inWatchlist ? 'remove' : 'add'}`, { media_id: $store.title.id, media_type: $store.title.media_type })"
                class="bg-(--primary) text-(--primary-foreground) border-(--primary) shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] hover:opacity-90 focus:opacity-90 flex items-center justify-center gap-2 border px-4 py-2 rounded-l-full text-sm font-medium transition-all duration-200 outline-none select-none cursor-pointer flex-1 h-9.5">
                <i class="fa-bookmark" :class="inWatchlist ? 'fa-solid' : 'fa-regular'"></i>
                <span x-text="inWatchlist ? 'Remove from Watchlist' : 'Add to Watchlist'"></span>
            </button>

            <div
                class="bg-(--primary) border-(--primary) w-px self-stretch border-y shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] h-9.5">
                <div class="bg-(--primary-foreground) h-full w-full opacity-20"></div>
            </div>

            <button @click="$dispatch('list:open-dialog', title)"
                class="bg-(--primary) text-(--primary-foreground) border-(--primary) shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] hover:opacity-90 focus:opacity-90 flex items-center justify-center border px-3 py-2 rounded-r-full text-sm transition-all duration-200 outline-none select-none cursor-pointer">
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </button>
        </div>
        {{-- ! --}}

        <x-ui.button
            @click="$dispatch(`watched:${inWatched ? 'remove' : 'add'}`, { media_id: $store.title.id, media_type: $store.title.media_type })"
            variant="outline" size="lg" class="rounded-full! cursor-pointer!">
            <i :class="inWatched ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle-check'"></i>
            <span x-text="inWatched ? 'Mark as Unwatched' : 'Mark as Watched'"></span>
        </x-ui.button>
    @else
        <a href="/login"
            class="bg-(--primary) text-(--primary-foreground) border-(--primary) shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] hover:opacity-90 flex items-center justify-center gap-2 border px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 w-full">
            <i class="fa-regular fa-bookmark"></i>
            <span>Add to Watchlist</span>
        </a>

        <a href="/login"
            class="flex items-center justify-center gap-2 border px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 w-full border-(--input)">
            <i class="fa-regular fa-circle-check"></i>
            <span>Mark as Watched</span>
        </a>
    @endauth

    <div class="flex gap-2 text-sm pt-1">
        <a :href="`https://www.imdb.com/title/${title.imdb_id}/reviews`" target="_blank"
            class="text-blue-500 dark:text-blue-400 font-medium hover:underline"
            x-text="`${title.vote_count?.toLocaleString()} User reviews`"></a>
    </div>

    <p x-show="title.tagline" class="text-sm italic text-(--muted-foreground)"
        x-text="`&ldquo;${title.tagline}&rdquo;`">
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
        <a :href="`https://streamex.sh/watch/${title.media_type}/${title.id}`" target="_blank" class="contents">
            <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                <span>Watch on <strong>StreameX</strong></span>
            </x-ui.button>
        </a>

        <a href="#" :data-title="title.title || title.name" x-data="telegram" x-on:click.prevent="share"
            class="contents">
            <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                <span>Watch on <strong>Telegram</strong></span>
            </x-ui.button>
        </a>

        <template x-if="title.imdb_id">
            <a :href="`https://www.imdb.com/title/${title.imdb_id}`" target="_blank" class="contents">
                <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                    <span>View on <strong>IMDB</strong></span>
                </x-ui.button>
            </a>
        </template>

        <a :href="`https://www.themoviedb.org/${$store.title.media_type}/${title.id}`" target="_blank" class="contents">
            <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                <span>View on <strong>TMDB</strong></span>
            </x-ui.button>
        </a>
        <a :href="`http://watchlist.test/api/${$store.title.media_type}/${title.id}`" target="_blank" class="contents">
            <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                <span>View on <strong>Watchlist</strong></span>
            </x-ui.button>
        </a>
    </div>
</div>
