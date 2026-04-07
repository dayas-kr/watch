<div x-data="similar($store.title.id, $store.title.media_type)" x-intersect.once="fetch" {{ $attributes }}>
    <!-- Loading -->
    <template x-if="loading && !error">
        <div class="flex gap-4 overflow-x-auto no-scrollbar">
            <template x-for="i in 8" :key="i">
                <div class="shrink-0 flex flex-col space-y-2 w-48">
                    <div class="bg-(--muted) rounded-xl aspect-2/3"></div>
                    <div class="space-y-1">
                        <div class="h-4.5 bg-(--muted) rounded"></div>
                        <div class="h-3.5 bg-(--muted) w-3/4 rounded"></div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- Error -->
    <template x-if="error">
        <div class="text-red-500 dark:text-red-400 text-sm">Something went wrong.</div>
    </template>

    <!-- Content -->
    <template x-if="!loading && !error && results.length">
        <x-ui.horizontal-slider>
            <template x-for="title in results" :key="title.id">
                <a :href="`/${$store.title.media_type}/${title.id}`"
                    class="shrink-0 flex flex-col space-y-2 w-46 group/card">
                    <!-- Poster -->
                    <div class="bg-(--muted) rounded-xl aspect-2/3 relative overflow-hidden">
                        <template x-if="title.poster_path">
                            <img :src="`https://image.tmdb.org/t/p/w300${title.poster_path}`"
                                :alt="title.title || title.name"
                                class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105 z-1">
                        </template>

                        <div class="w-full h-full flex items-center justify-center z-0">
                            <i class="fa-regular fa-image text-5xl text-(--muted-foreground)/25"></i>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="space-y-0.5">
                        <div x-text="title.title || title.name"
                            class="line-clamp-2 font-medium text-sm group-focus-visible/card:underline underline-offset-2">
                        </div>

                        <div class="flex items-center gap-1.5 text-xs text-(--muted-foreground)">
                            <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
                            <span x-text="title.vote_average.toFixed(1)" class="font-medium text-(--foreground)"></span>
                            <span>·</span>
                            <span x-text="dayjs(title.release_date || title.first_air_date).format('YYYY')"></span>
                        </div>
                    </div>
                </a>
            </template>
        </x-ui.horizontal-slider>
    </template>
</div>
