<div x-data="search" class="relative" @keydown.meta.k.window.prevent="$refs.searchInput.focus()"
    @keydown.ctrl.k.window.prevent="$refs.searchInput.focus()">
    <div class="grid grid-cols-[auto_1fr_auto] items-center gap-2 h-9 border border-(--border) rounded-xl">
        <button @click="toggleSourcesDialog" x-ref="sourcesButton"
            class="border-r border-(--border) h-full px-3 flex items-center text-sm font-medium cursor-pointer select-none">
            <span x-text="activeSourceLabel"></span>
            <x-lucide-chevron-down class="ml-1.5 size-4 text-(--muted-foreground)" />
        </button>
        <input x-model="query" @input.debounce.500ms="fetchResults" @keydown.down.prevent="moveSelection(1)"
            @keydown.up.prevent="moveSelection(-1)" @keydown.enter.prevent="enterSelect" @focus="handleInputFocus"
            @blur="handleInputBlur" @keydown.escape="handleEscape" x-ref="searchInput" type="text" autocomplete="off"
            spellcheck="false" :placeholder="placeholder"
            class="bg-transparent border-0 focus:ring-0 placeholder:text-(--muted-foreground) p-0 text-sm">
        <div class="h-full px-3 flex items-center pointer-events-none">
            <x-lucide-search class="text-(--muted-foreground) size-4" />
        </div>
    </div>

    <!-- Sources Dialog -->
    <div x-show="sourcesDialogOpen" x-trap.noScroll="sourcesDialogOpen" @keydown.down="$focus.next()"
        @keydown.up="$focus.previous()" x-anchor.bottom-start.offset.4="$refs.sourcesButton"
        @click.outside="closeSourcesDialog" @keydown.escape.window="closeSourcesDialog" x-transition
        class="bg-(--popover) border border-(--border) shadow p-1 flex flex-col rounded-xl min-w-52 z-50">
        <template x-for="(option, index) in sources" :key="index">
            <button @click="onSourceChange(option.value)" @mouseenter="$el.focus()"
                class="text-sm font-medium px-2.5 py-1.5 focus:bg-(--muted) focus:outline-none text-left rounded-md flex items-center gap-2 cursor-pointer select-none">
                <i :class="option.icon" class="text-(--muted-foreground)"></i>
                <span x-text="option.label"></span>
            </button>
        </template>
        <div class="grid border-t border-(--border) pt-1 mt-1">
            <a href="#" @mouseenter="$el.focus()"
                class="text-sm font-medium px-2.5 py-1.5 focus:bg-(--muted) focus:outline-none text-left rounded-md flex items-center justify-between gap-2 cursor-pointer select-none">
                <div class="bg-(--border) size-6 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-search text-xs text-(--muted-foreground)"></i>
                </div>
                <span>Advanced Search</span>
                <i class="fa-solid fa-chevron-right text-xs text-(--muted-foreground)"></i>
            </a>
        </div>
    </div>

    <!-- Results Dropdown -->
    <div x-show="open"
        class="absolute top-10 max-h-[calc(100vh-3.5rem)] w-full rounded-xl z-50 bg-(--popover) border border-(--border) shadow overflow-hidden flex flex-col">
        <!-- Loading indicator -->
        <div x-show="loading" class="py-12 flex items-center justify-center">
            <div class="flex items-center gap-2">
                <div
                    class="size-3.5 rounded-full bg-(--ring)/75 animate-[dot-bounce_1s_ease-in-out_infinite] [animation-delay:-0.3s]">
                </div>
                <div
                    class="size-3.5 rounded-full bg-(--ring)/75 animate-[dot-bounce_1s_ease-in-out_infinite] [animation-delay:-0.15s]">
                </div>
                <div class="size-3.5 rounded-full bg-(--ring)/75 animate-[dot-bounce_1s_ease-in-out_infinite]"></div>
            </div>
        </div>

        <!-- Empty state -->
        <div x-show="!loading && results.length === 0 && query.trim()"
            class="py-12 flex flex-col items-center justify-center gap-1 text-sm text-(--muted-foreground) select-none">
            <i class="fa-regular fa-face-frown text-2xl mb-1"></i>
            No results found for <strong x-text='`"${query}"`'></strong>
        </div>

        <div class="flex flex-col divide-y divide-(--border) overflow-y-auto no-scrollbar">
            {{-- ── Multi cards (movie + tv + person mixed) ─────────────────────── --}}
            <template x-if="!loading && activeResultType === 'multi'">
                <template x-for="(item, index) in results" :key="item.id">
                    <a :href="itemHref(item)" @mouseenter="selectedIndex = index"
                        :data-multi-card="index === selectedIndex" :data-active="index === selectedIndex"
                        class="flex items-center gap-2 shrink-0 px-2 py-1.75 data-[active=true]:bg-(--muted) transition-colors cursor-pointer select-none">
                        <!-- Thumbnail -->
                        <div class="aspect-2/3 bg-(--border) w-14 rounded-sm relative overflow-hidden shrink-0">
                            {{-- Person branch --}}
                            <template x-if="item.media_type === 'person'">
                                <template x-if="avatarUrl(item.profile_path)">
                                    <img :src="avatarUrl(item.profile_path)" :alt="item.name"
                                        class="w-full h-full object-cover pointer-events-none">
                                </template>
                            </template>
                            <template x-if="item.media_type === 'person' && !avatarUrl(item.profile_path)">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-user text-2xl text-(--muted-foreground)/25"></i>
                                </div>
                            </template>

                            {{-- Movie / TV branch --}}
                            <template x-if="item.media_type !== 'person' && item.poster_path">
                                <img :src="posterUrl(item.poster_path)" :alt="item.title || item.name"
                                    class="w-full h-full object-cover pointer-events-none">
                            </template>
                            <template x-if="item.media_type !== 'person' && !item.poster_path">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-regular fa-image text-2xl text-(--muted-foreground)/25"></i>
                                </div>
                            </template>
                        </div>

                        <!-- Meta -->
                        <div class="flex-1 flex flex-col gap-1 min-w-0 pointer-events-none">
                            <div x-text="item.title || item.name" class="font-medium truncate"></div>
                            {{-- Person: department + known-for title --}}
                            <template x-if="item.media_type === 'person'">
                                <div class="flex flex-col gap-0.5">
                                    <div x-text="item.known_for_department ?? '—'"
                                        class="text-sm text-(--muted-foreground)"></div>
                                    <div x-text="item.known_for?.[0]?.title || item.known_for?.[0]?.name || ''"
                                        class="text-sm text-(--muted-foreground) truncate"></div>
                                </div>
                            </template>

                            {{-- Movie / TV: year + type label --}}
                            <template x-if="item.media_type !== 'person'">
                                <div class="flex flex-col gap-0.5">
                                    <div x-text="releaseYear(item)" class="text-sm text-(--muted-foreground)"></div>
                                    <div x-text="mediaTypeLabel(item)" class="text-sm text-(--muted-foreground)"></div>
                                </div>
                            </template>
                        </div>

                        <!-- Media-type badge pill -->
                        <span
                            class="shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full border border-(--border) text-(--muted-foreground) pointer-events-none"
                            x-text="mediaTypeLabel(item)"></span>
                    </a>
                </template>
            </template>

            {{-- ── Title cards (movie / tv — single-source) ───────────────────── --}}
            <template x-if="!loading && activeResultType === 'title'">
                <template x-for="(item, index) in results" :key="item.id">
                    <a :href="itemHref(item)" :data-active="index === selectedIndex"
                        :data-movie-card="index === selectedIndex" :data-tv-card="index === selectedIndex"
                        class="flex items-center gap-2 shrink-0 px-2 py-1.75 hover:bg-(--muted) data-[active=true]:bg-(--muted) transition-colors cursor-pointer select-none">
                        <!-- Poster -->
                        <div class="aspect-2/3 bg-(--border) w-14 rounded-sm relative overflow-hidden shrink-0">
                            <template x-if="item.poster_path">
                                <img :src="posterUrl(item.poster_path)" :alt="item.title || item.name"
                                    class="w-full h-full object-cover pointer-events-none">
                            </template>
                            <template x-if="!item.poster_path">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-regular fa-image text-2xl text-(--muted-foreground)/25"></i>
                                </div>
                            </template>
                        </div>
                        <!-- Meta -->
                        <div class="flex-1 flex flex-col gap-1 min-w-0 pointer-events-none">
                            <div x-text="item.title || item.name" class="font-medium truncate"></div>
                            <div x-text="releaseYear(item)" class="text-sm text-(--muted-foreground)"></div>
                            <div x-text="mediaTypeLabel(item)" class="text-sm text-(--muted-foreground)"></div>
                        </div>
                    </a>
                </template>
            </template>

            {{-- ── Person cards ─────────────────────────────────────────────────── --}}
            <template x-if="!loading && activeResultType === 'person'">
                <template x-for="(item, index) in results" :key="item.id">
                    <a :href="itemHref(item)" :data-active="index === selectedIndex"
                        :data-person-card="index === selectedIndex"
                        class="flex items-center gap-2 shrink-0 px-2 py-1.75 hover:bg-(--muted) data-[active=true]:bg-(--muted) transition-colors cursor-pointer select-none">
                        <!-- Profile photo -->
                        <div class="aspect-2/3 bg-(--border) w-14 rounded-sm relative overflow-hidden shrink-0">
                            <template x-if="avatarUrl(item.profile_path)">
                                <img :src="avatarUrl(item.profile_path)" :alt="item.name"
                                    class="w-full h-full object-cover pointer-events-none">
                            </template>
                            <template x-if="!avatarUrl(item.profile_path)">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-user text-2xl text-(--muted-foreground)/25"></i>
                                </div>
                            </template>
                        </div>
                        <!-- Meta -->
                        <div class="flex-1 flex flex-col gap-1 min-w-0 pointer-events-none">
                            <div x-text="item.name" class="font-medium truncate"></div>
                            <div x-text="item.known_for_department ?? '—'" class="text-sm text-(--muted-foreground)">
                            </div>
                            <div x-text="item.known_for?.[0]?.title || item.known_for?.[0]?.name || ''"
                                class="text-sm text-(--muted-foreground) truncate"></div>
                        </div>
                    </a>
                </template>
            </template>

            {{-- ── Collection cards ─────────────────────────────────────────────── --}}
            <template x-if="!loading && activeResultType === 'collection'">
                <template x-for="(item, index) in results" :key="item.id">
                    <a :href="itemHref(item)" :data-active="index === selectedIndex"
                        :data-collection-card="index === selectedIndex"
                        class="flex items-center gap-2 shrink-0 px-2 py-1.75 hover:bg-(--muted) data-[active=true]:bg-(--muted) transition-colors cursor-pointer select-none">
                        <div class="aspect-2/3 bg-(--border) w-14 rounded-sm relative overflow-hidden shrink-0">
                            <template x-if="item.poster_path">
                                <img :src="posterUrl(item.poster_path)" :alt="item.name"
                                    class="w-full h-full object-cover pointer-events-none">
                            </template>
                            <template x-if="!item.poster_path && item.backdrop_path">
                                <img :src="posterUrl(item.backdrop_path)" :alt="item.name"
                                    class="w-full h-full object-cover object-center scale-150 pointer-events-none">
                            </template>
                            <template x-if="!item.poster_path && !item.backdrop_path">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-layer-group text-xl text-(--muted-foreground)/25"></i>
                                </div>
                            </template>
                        </div>
                        <div class="flex-1 flex flex-col gap-1 min-w-0 pointer-events-none">
                            <div x-text="item.name" class="font-medium truncate"></div>
                            <div class="text-sm text-(--muted-foreground)">Collection</div>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-xs text-(--muted-foreground) shrink-0 mr-1 pointer-events-none"></i>
                    </a>
                </template>
            </template>

            {{-- ── Company cards ────────────────────────────────────────────────── --}}
            <template x-if="!loading && activeResultType === 'company'">
                <template x-for="(item, index) in results" :key="item.id">
                    <a :href="itemHref(item)" :data-active="index === selectedIndex"
                        :data-company-card="index === selectedIndex"
                        class="flex items-center gap-3 shrink-0 px-3 py-2 hover:bg-(--muted) data-[active=true]:bg-(--muted) transition-colors cursor-pointer select-none">
                        <div
                            class="w-14 h-10 rounded-md bg-white flex items-center justify-center p-1.5 shrink-0 border border-(--border)">
                            <template x-if="item.logo_path">
                                <img :src="logoUrl(item.logo_path)" :alt="item.name"
                                    class="max-w-full max-h-full object-contain pointer-events-none">
                            </template>
                            <template x-if="!item.logo_path">
                                <i class="fa-solid fa-building text-lg text-gray-300"></i>
                            </template>
                        </div>
                        <div class="flex-1 flex flex-col gap-0.5 min-w-0 pointer-events-none">
                            <div x-text="item.name" class="font-medium truncate"></div>
                            <div class="flex items-center gap-1.5 text-sm text-(--muted-foreground)">
                                <span>Production Company</span>
                                <template x-if="item.origin_country">
                                    <span x-text="`· ${item.origin_country}`"></span>
                                </template>
                            </div>
                        </div>
                    </a>
                </template>
            </template>

            {{-- ── Keyword chips ─────────────────────────────────────────────────── --}}
            <template x-if="!loading && activeResultType === 'keyword'">
                <div class="flex flex-wrap gap-2 p-3">
                    <template x-for="(item, index) in results" :key="item.id">
                        <a :href="itemHref(item)" :data-active="index === selectedIndex"
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border border-(--border)
                                   bg-(--muted)/50 hover:bg-(--muted) data-[active=true]:bg-(--muted)
                                   text-sm font-medium transition-colors cursor-pointer select-none">
                            <i class="fa-solid fa-tag text-xs text-(--muted-foreground)"></i>
                            <span x-text="item.name"></span>
                        </a>
                    </template>
                </div>
            </template>
        </div>

        <!-- "See all" footer -->
        <a x-show="!loading && results.length > 0" href="#"
            class="block text-sm px-3 py-1.75 font-medium hover:bg-(--muted) border-t border-(--border) shrink-0 cursor-pointer select-none">
            See all results for <strong x-text='`"${query}"`'></strong>
        </a>
    </div>
</div>
