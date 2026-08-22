<x-base-layout :title="'List'">
    <div class="flex flex-col min-h-screen font-body">
        <x-header />
        <main x-data="list(@js($list_id))" class="flex-1">
            <!-- Initial Loading -->
            <div x-show="loading && !items.length" x-cloak class="max-w-7xl px-4 py-8 mx-auto sm:px-3 sm:py-6">
                Loading...
            </div>

            <!-- Error State -->
            <div x-show="error" x-cloak class="max-w-7xl px-4 py-8 mx-auto sm:px-3 sm:py-6">
                <span class="text-sm text-(--muted-foreground) flex items-center gap-2">
                    Something went wrong.
                    <button @click="fetch()" class="underline text-(--foreground) underline-offset-2 font-medium">
                        Retry
                    </button>
                </span>
            </div>

            <!-- Content -->
            <div x-show="data" class="max-w-7xl px-4 py-8 mx-auto sm:px-3 sm:py-6">
                <!-- Page Header -->
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-semibold text-(--foreground) truncate" x-text="data?.name"></h1>

                    <a href="{{ route('lists.index') }}" class="contents">
                        <x-ui.button variant="ghost" class="rounded-xl! ml-auto">
                            View Lists
                        </x-ui.button>
                    </a>

                    <div x-data="{ open: false }" @keydown.escape.window="open = false">
                        <x-ui.button x-ref="trigger" @click="open = !open" variant="ghost" size="icon"
                            x-bind:aria-expanded="open" class="rounded-full!">
                            <x-lucide-ellipsis />
                        </x-ui.button>

                        <div x-anchor.bottom-end.offset.6="$refs.trigger" x-cloak x-show="open" x-transition
                            x-trap.noScroll="open" @click.outside="open = false"
                            class="bg-(--popover) rounded-xl p-1 border border-(--border) min-w-36 z-50 shadow-xs">
                            <button @mouseover="$el.focus()" @click="$dispatch('list:clear', data.id); open = false"
                                class="focus:bg-(--muted) focus:outline-none w-full px-2.5 py-1.25 flex items-center gap-2 rounded-lg text-(--secondary-foreground) text-sm font-medium">
                                <i class="fa-solid fa-xmark text-sm"></i>
                                Clear
                            </button>
                            <button @mouseover="$el.focus()"
                                class="focus:bg-(--muted) focus:outline-none w-full px-2.5 py-1.25 flex items-center gap-2 rounded-lg text-(--secondary-foreground) text-sm font-medium">
                                <i class="fa-regular fa-pen-to-square text-sm"></i>
                                Edit
                            </button>
                            <hr class="border-(--border)/75 my-1">
                            <button @mouseover="$el.focus()" @click="$dispatch('list:delete', data.id); open = false"
                                class="focus:bg-(--destructive)/10 focus:text-(--destructive) focus:outline-none w-full px-2.5 py-1.25 flex items-center gap-2 rounded-lg text-(--secondary-foreground) text-sm font-medium">
                                <i class="fa-regular fa-trash-can text-sm"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <template x-if="data?.description">
                    <span class="text-sm text-(--muted-foreground)" x-text="data?.description"></span>
                </template>

                <!-- List Meta -->
                <template x-if="data">
                    <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1.5 mb-4">
                        <span class="inline-flex items-center gap-1 text-sm text-(--muted-foreground) font-medium">
                            <i class="fa-solid fa-film text-xs"></i>
                            <span
                                x-text="`${data.total_results ?? 0} item${(data.total_results ?? 0) === 1 ? '' : 's'}`"></span>
                        </span>

                        <span class="text-(--muted-foreground) font-bold">&middot;</span>

                        <span
                            class="inline-flex items-center gap-1 text-sm text-(--muted-foreground) font-medium uppercase"
                            x-text="data.iso_639_1"></span>
                    </div>
                </template>

                <!-- Items Grid -->
                <div x-show="items.length"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6 md:gap-8 gap-y-8">
                    <template x-for="(title, index) in items" :key="title.id">
                        <div class="shrink-0 flex flex-col space-y-2 group/card">
                            <!-- Poster -->
                            <a :href="`/${title.media_type}/${title.id}`"
                                class="bg-(--muted) rounded-xl aspect-2/3 relative overflow-hidden group/img">

                                <template x-if="title.poster_path">
                                    <img :src="`https://image.tmdb.org/t/p/w300${title.poster_path}`"
                                        :alt="title.title || title.name"
                                        class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105 z-1" />
                                </template>

                                <div class="w-full h-full flex items-center justify-center z-0">
                                    <i class="fa-regular fa-image text-5xl text-(--muted-foreground)/25"></i>
                                </div>

                                <!-- Hover actions -->
                                <div @click.prevent
                                    class="z-10 bg-neutral-500/50 absolute bottom-0 left-0 right-0 backdrop-blur-sm
                                       group-hover/img:translate-y-0 flex flex-col gap-2 p-2
                                       translate-y-full transition-all cursor-default">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="$dispatch('list:remove', { list_id: data.id, media_id: title.id, media_type: title.media_type })"
                                            class="h-7 w-full bg-neutral-500 hover:bg-red-400 rounded-full flex items-center
                                               justify-center gap-2 text-white text-sm font-medium focus:outline-none">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </a>

                            <!-- Info -->
                            <div class="space-y-0.5">
                                <a :href="`/${title.media_type}/${title.id}`" x-text="title.title || title.name"
                                    class="line-clamp-2 font-medium text-sm hover:underline underline-offset-2">
                                </a>

                                <div class="flex items-center gap-1.5 text-sm text-(--muted-foreground)">
                                    <i class="fa-solid fa-star text-yellow-500 text-[9px]"></i>
                                    <span x-text="title.vote_average?.toFixed(1)" class="font-medium"></span>
                                    <span>&middot;</span>
                                    <span
                                        x-text="dayjs(title.first_air_date || title.release_date).format('YYYY')"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="initialized && items.length === 0 && !loading && !error" x-cloak
                    class="py-8 flex justify-center">
                    <span class="text-sm text-(--muted-foreground)">
                        This list has no items yet.
                    </span>
                </div>

                <!-- Infinite Scroll Sentinel -->
                <div x-intersect.threshold.10="fetchMore()" class="col-span-full py-8 flex justify-center">
                    <template x-if="loading && items.length">
                        <span class="text-sm text-(--muted-foreground) flex items-center gap-2">
                            <x-lucide-loader-circle class="size-5 animate-spin" />
                            Loading...
                        </span>
                    </template>

                    <template x-if="initialized && items.length > 0 && !hasMore() && !loading">
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
    <x-titles.list-manager />

    @push('head')
        @vite('resources/js/pages/list.js')
        <x-cdn.font-awesome />
        <x-cdn.dayjs />
    @endpush

    @push('styles')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600&display=swap');

            .font-display {
                font-family: 'DM Serif Display', serif;
            }
        </style>
    @endpush
</x-base-layout>
