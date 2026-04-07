<div {{ $attributes->merge(['class' => 'flex items-center gap-10']) }}>
    <div>
        <div class="text-xs uppercase tracking-wider text-(--muted-foreground) mb-1">IMDb
            Rating</div>
        <div class="flex items-center gap-1.5">
            <i class="fa-solid fa-star text-yellow-400 shrink-0 text-sm"></i>
            <span class="flex items-center gap-0.5">
                <span class="font-bold" x-text="title.vote_average?.toFixed(1)"></span>
                <span class="font-medium text-(--muted-foreground)">/10</span>
            </span>
            <template x-if="title.vote_average">
                <span class="text-sm text-(--muted-foreground)" x-text="formatNumeral(title.vote_count)"></span>
            </template>
        </div>
    </div>
    <div class="block">
        <div class="text-xs uppercase tracking-wider text-(--muted-foreground) mb-1">Your
            Rating</div>
        <button
            class="flex items-center gap-1.5 text-blue-500 dark:text-blue-400 font-semibold transition-colors group">
            <span class="hidden group-hover:block">
                <i class="fa-solid fa-star text-blue-400 hover:text-blue-300"></i>
            </span>
            <span class="group-hover:hidden">
                <i class="fa-regular fa-star text-blue-400 hover:text-blue-300"></i>
            </span>
            Rate
        </button>
    </div>

    <div class="block">
        <div class="text-xs uppercase tracking-wider text-(--muted-foreground) mb-1">Popularity
        </div>
        <span class="font-bold" x-text="Math.round(title.popularity)"></span>
    </div>
</div>
