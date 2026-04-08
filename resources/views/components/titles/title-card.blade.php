<div class="shrink-0 flex flex-col space-y-2 w-48 group/card">
    <!-- Poster -->
    <a :href="`/${title.media_type || key}/${title.id}`"
        class="bg-(--muted) rounded-xl aspect-2/3 relative overflow-hidden">
        <template x-if="title.poster_path">
            <img :src="`https://image.tmdb.org/t/p/w300${title.poster_path}`" :alt="title.title || title.name"
                class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105 z-1">
        </template>

        <div class="w-full h-full flex items-center justify-center z-0">
            <i class="fa-regular fa-image text-5xl text-(--muted-foreground)/25"></i>
        </div>
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
