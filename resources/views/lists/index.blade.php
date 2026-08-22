<x-base-layout title="Lists">
    <div class="flex flex-col min-h-screen font-body">
        <x-header />
        <main x-data="lists" @list:add-new.window="addNewCreatedList($event.detail)" class="flex-1">
            <div class="max-w-7xl px-4 py-8 mx-auto sm:px-3 sm:py-6">
                <!-- Page Header -->
                <div class="grid grid-cols-[1fr_auto] items-center gap-x-3 gap-y-2 mb-4 md:mb-6 lg:mb-8">
                    <h1 class="text-xl sm:text-2xl font-semibold text-(--foreground)">
                        My Lists
                    </h1>

                    <x-ui.button @click="$dispatch('list:open-dialog')" variant="secondary">
                        <x-lucide-plus />New List
                    </x-ui.button>
                </div>

                <!-- Error State -->
                <div x-show="error" x-cloak class="py-8 flex justify-center">
                    <span class="text-sm text-(--muted-foreground) flex items-center gap-2">
                        Something went wrong.
                        <button @click="fetch()" class="underline text-(--foreground) underline-offset-2 font-medium">
                            Retry
                        </button>
                    </span>
                </div>

                <!-- Lists Grid -->
                <div x-show="data.length" class="grid md:grid-cols-2 gap-4">
                    <template x-for="(list, index) in data" :key="list.id">
                        <div class="shrink-0 flex flex-col space-y-2 group/card">
                            <!-- Poster -->
                            <a :href="`/lists/${list.id}`"
                                class="bg-(--muted) rounded-xl aspect-video relative overflow-hidden group/img">

                                <template x-if="list.poster_path">
                                    <img :src="`https://image.tmdb.org/t/p/w300${list.poster_path}`"
                                        :alt="list.name"
                                        class="w-full h-full object-cover transition duration-300 group-hover/card:scale-105 z-1" />
                                </template>

                                <div class="w-full h-full flex items-center justify-center z-0">
                                    <i class="fa-regular fa-image text-[6rem] text-(--muted-foreground)/15"></i>
                                </div>

                                <!-- Item count overlay -->
                                <div class="absolute bottom-2 right-2 z-10">
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-medium bg-black/60 text-white backdrop-blur-sm px-1.5 py-1 rounded-lg">
                                        <i class="fa-solid fa-film text-sm"></i>
                                        <span x-text="list.item_count"></span>
                                    </span>
                                </div>
                            </a>

                            <!-- Info -->
                            <div class="space-y-0.5">
                                <a :href="`/lists/${list.id}`" x-text="list.name"
                                    class="line-clamp-2 font-medium hover:underline underline-offset-2">
                                </a>

                                <div class="flex items-center gap-1.5 text-sm text-(--muted-foreground)">
                                    <template x-if="list.description">
                                        <span x-text="list.description" class="line-clamp-2 font-medium"></span>
                                    </template>
                                    <template x-if="!list.description">
                                        <span class="font-medium">No description</span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="initialized && data.length === 0 && !loading && !error" x-cloak
                    class="py-8 flex justify-center">
                    <span class="text-sm text-(--muted-foreground)">
                        You haven't created any lists yet.
                    </span>
                </div>

                <!-- Infinite Scroll Sentinel -->
                <div x-intersect.threshold.10="fetch()" class="col-span-full py-8 flex justify-center">
                    <template x-if="loading">
                        <span class="text-sm text-(--muted-foreground) flex items-center gap-2">
                            <x-lucide-loader-circle class="size-5 animate-spin" />
                            Loading...
                        </span>
                    </template>

                    <template x-if="initialized && data.length > 0 && !hasMore() && !loading">
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
        @vite('resources/js/pages/lists.js')
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
