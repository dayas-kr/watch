<div {{ $attributes->merge(['class' => 'flex flex-col gap-3']) }}>
    <button
        @click="$dispatch(`watchlist:${inWatchlist ? 'remove' : 'add'}`, { media_id: $store.title.id, media_type: $store.title.media_type, page: $store.title.media_type + '.show' })"
        class="bg-(--primary) text-(--primary-foreground) border-(--primary) shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] hover:opacity-90 focus:opacity-90 flex items-center justify-center gap-2 border px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 outline-none select-none cursor-pointer active:not-aria-[haspopup]:translate-y-px w-full">
        <i class="fa-bookmark" :class="inWatchlist ? 'fa-solid' : 'fa-regular'"></i>
        <span x-text="inWatchlist ? 'Remove from Watchlist' : 'Add to Watchlist'"></span>
    </button>

    <div class="flex gap-2 text-sm pt-1">
        <a :href="`https://www.imdb.com/title/${title.imdb_id}/reviews`" target="_blank"
            class="text-blue-500 dark:text-blue-400 font-medium hover:underline"
            x-text="`${title.vote_count?.toLocaleString()} User reviews`"></a>
    </div>

    <p x-show="title.tagline" class="text-sm italic text-(--muted-foreground)" x-text="`&ldquo;${title.tagline}&rdquo;`">
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
        <a :href="`https://streamex.sh/watch/movie/${title.id}`" target="_blank" class="contents">
            <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                <span>Watch on <strong>StreameX</strong></span>
            </x-ui.button>
        </a>

        <a href="#" :data-title="title.title || title.name" x-data="telegram" x-on:click.prevent="share"
            class="contents">
            <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                <span>Share on <strong>Telegram</strong></span>
            </x-ui.button>
        </a>

        <template x-if="title.imdb_id">
            <a :href="`https://www.imdb.com/title/${title.imdb_id}`" target="_blank" class="contents">
                <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                    <span>View on <strong>IMDB</strong></span>
                </x-ui.button>
            </a>
        </template>

        <a :href="`https://www.themoviedb.org/movie/${title.id}`" target="_blank" class="contents">
            <x-ui.button variant="secondary" size="lg" class="rounded-full! cursor-pointer!">
                <span>View on <strong>TMDB</strong></span>
            </x-ui.button>
        </a>
    </div>
</div>
