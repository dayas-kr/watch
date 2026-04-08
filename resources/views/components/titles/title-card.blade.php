<div x-data="titleCard(title)" :data-title-id="title.id" :data-watchlist="inWatchlist ? 'true' : 'false'"
    @title-card:sync-watchlist.window="updateWatchlist($event)" data-slot="title-card"
    class="shrink-0 flex flex-col space-y-2 w-48 group/card">
    <!-- Poster -->
    <a :href="`/${title.media_type}/${title.id}`"
        class="bg-(--muted) rounded-xl aspect-2/3 relative overflow-hidden group/img">
        <template x-if="title.poster_path">
            <img :src="`https://image.tmdb.org/t/p/w300${title.poster_path}`" :alt="title.title || title.name"
                class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105 z-1">
        </template>

        <div class="w-full h-full flex items-center justify-center z-0">
            <i class="fa-regular fa-image text-5xl text-(--muted-foreground)/25"></i>
        </div>

        <button
            @click.prevent="$dispatch('watchlist:add', { media_id: title.id, media_type: title.media_type, page: 'welcome' })"
            class="hidden group-data-[watchlist=false]/card:group-hover/card:block absolute top-0 left-0 w-8 h-12 bg-neutral-500/75 hover:bg-neutral-500/85 shadow-sm shadow-black text-white items-center justify-center cursor-pointer"
            style="clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 50% 82%, 0% 100%);">
            <i class="fa-solid fa-plus text-lg text-shadow-sm mb-2.5"></i>
        </button>

        <button
            @click.prevent="$dispatch('watchlist:remove', { media_id: title.id, media_type: title.media_type, page: 'home' })"
            class="hidden group-data-[watchlist=true]/card:group-hover/card:block absolute top-0 left-0 py-1.75 px-1 rounded-br-lg backdrop-blur-sm bg-neutral-500/50"
            title="Remove from Watchlist">
            <i class="fa-solid fa-bookmark text-white text-xl text-shadow-2xs"></i>
        </button>
    </a>

    <!-- Info -->
    <div class="space-y-0.5">
        <a :href="`/${title.media_type || key}/${title.id}`" x-text="title.title || title.name"
            class="line-clamp-1 font-medium text-sm hover:underline underline-offset-2">
        </a>

        <div class="flex items-center gap-1.5 text-xs text-(--muted-foreground)">
            <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
            <span x-text="title.vote_average.toFixed(1)" class="font-medium text-(--foreground)"></span>
            <span>·</span>
            <span x-text="dayjs(title.release_date || title.first_air_date).format('YYYY')"></span>
        </div>
    </div>
</div>
