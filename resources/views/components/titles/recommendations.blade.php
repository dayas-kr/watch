<div x-data="recommendations($store.title.id, $store.title.media_type)" x-intersect.once="fetch" {{ $attributes }}>
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
                <x-titles.title-card />
            </template>
        </x-ui.horizontal-slider>
    </template>
</div>
