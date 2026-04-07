<x-base-layout>
    <div x-data="tv({{ $tv_id }})" x-init="initialSetup(@js($tv_id))" class="flex flex-col min-h-screen font-body">
        <x-header />
        <main class="flex-1">
            <!-- Loading -->
            <div x-show="loading" class="max-w-7xl px-4 mx-auto sm:px-3 py-6 space-y-4">
                Loading...
            </div>

            <!-- Content -->
            <div x-show="!loading && !error" class="max-w-7xl px-4 mx-auto sm:px-3 py-6 space-y-4">
                <div class="flex justify-between items-center gap-4">
                    <div class="space-y-1">
                        <!-- Title -->
                        <x-titles.h1-heading />

                        <div class="flex flex-col gap-0.75">
                            <!-- Original title -->
                            <x-titles.orginal-heading />

                            <!-- Year -->
                            <div class="flex items-center gap-2 text-sm font-medium text-(--muted-foreground)">
                                <span>TV Series</span>
                                <template x-if="title.first_air_date || title.release_date">
                                    <span class="before:content-['\00B7'] before:mr-2">
                                        <span
                                            x-text="dayjs(title.release_date || title.first_air_date).format('YYYY')"></span>
                                        <template x-if="title.last_air_date">
                                            <span style="display: contents">
                                                <span>-</span>
                                                <span x-text="dayjs(title.last_air_date).format('YYYY')"></span>
                                            </span>
                                        </template>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:block">
                        <x-titles.stats />
                    </div>
                </div>

                <div class="flex gap-2 sm:gap-3">
                    <div
                        class="hidden sm:block shrink-0 aspect-2/3 h-80 lg:h-96 rounded-xl overflow-hidden bg-(--muted) relative">
                        <img :src="`https://image.tmdb.org/t/p/w300${title.poster_path}`" :alt="title.title"
                            class="w-full h-full object-cover block" />
                    </div>

                    <!-- Backdrop + videos/photos buttons (mobile & tablet) -->
                    <div class="flex-1 min-w-0 flex flex-col gap-2 sm:gap-3">
                        <div
                            class="w-full aspect-video sm:aspect-auto sm:h-80 lg:h-96 rounded-xl overflow-hidden bg-(--muted) relative">
                            <img :src="`https://image.tmdb.org/t/p/w780${title.backdrop_path}`" :alt="title.title"
                                class="w-full h-full object-cover block" />
                            <div
                                class="absolute inset-0 flex items-end p-2.5 sm:p-3 bg-linear-to-t from-black/75 via-transparent to-transparent">
                                <button
                                    class="flex items-center gap-2 text-white bg-linear-to-b from-transparent to-transparent hover:from-transparent hover:to-white/10 focus:from-transparent focus:to-white/10 border border-transparent hover:border-white/25 focus:border-white/25 transition-all duration-200 focus:shadow-sm hover:shadow-sm hover:backdrop-blur-md focus:backdrop-blur-md pl-1.5 pr-3.5 py-1.5 rounded-full select-none outline-none">
                                    <x-lucide-play-circle class="size-6 sm:size-8" stroke-width="1.5" />
                                    <span class="sm:text-lg font-semibold drop-shadow">Play trailer</span>
                                </button>
                            </div>
                        </div>

                        <!-- Videos + Photos buttons: mobile & tablet only (hidden lg+) -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-3 lg:hidden">
                            <x-ui.button size="lg" variant="secondary" class="rounded-full!">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    class="text-(--muted-foreground)" viewBox="0 0 24 24" fill="currentColor"
                                    role="presentation">
                                    <path
                                        d="M3 6c-.55 0-1 .45-1 1v13c0 1.1.9 2 2 2h13c.55 0 1-.45 1-1s-.45-1-1-1H5c-.55 0-1-.45-1-1V7c0-.55-.45-1-1-1zm17-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l5.47 4.1c.27.2.27.6 0 .8L12 14.5z">
                                    </path>
                                </svg>
                                <span x-text="`${(title.videos?.results?.length ?? 0)} VIDEOS`"></span>
                            </x-ui.button>
                            <x-ui.button size="lg" variant="secondary" class="rounded-full!">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    class="text-(--muted-foreground)" viewBox="0 0 24 24" fill="currentColor"
                                    role="presentation">
                                    <path fill="none" d="M0 0h24v24H0V0z"></path>
                                    <path
                                        d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-10.6-3.47l1.63 2.18 2.58-3.22a.5.5 0 0 1 .78 0l2.96 3.7c.26.33.03.81-.39.81H9a.5.5 0 0 1-.4-.8l2-2.67c.2-.26.6-.26.8 0zM2 7v13c0 1.1.9 2 2 2h13c.55 0 1-.45 1-1s-.45-1-1-1H5c-.55 0-1-.45-1-1V7c0-.55-.45-1-1-1s-1 .45-1 1z">
                                    </path>
                                </svg>
                                <span x-text="`${(title.images?.results?.length ?? 0)} PHOTOS`"></span>
                            </x-ui.button>
                        </div>
                    </div>

                    <!-- Videos + Photos sidebar: desktop only (lg+) -->
                    <div class="hidden lg:flex flex-col gap-3 shrink-0 w-40 h-96">
                        <button
                            class="flex-1 rounded-xl bg-(--muted) flex flex-col items-center justify-center gap-2 hover:opacity-70 transition-opacity cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                class="size-8 text-(--muted-foreground)" viewBox="0 0 24 24" fill="currentColor"
                                role="presentation">
                                <path
                                    d="M3 6c-.55 0-1 .45-1 1v13c0 1.1.9 2 2 2h13c.55 0 1-.45 1-1s-.45-1-1-1H5c-.55 0-1-.45-1-1V7c0-.55-.45-1-1-1zm17-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l5.47 4.1c.27.2.27.6 0 .8L12 14.5z">
                                </path>
                            </svg>
                            <span class="text-sm font-semibold uppercase"
                                x-text="`${title.videos?.results?.length ?? 0} Videos`"></span>
                        </button>
                        <button
                            class="flex-1 rounded-xl bg-(--muted) flex flex-col items-center justify-center gap-2 hover:opacity-70 transition-opacity cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                class="size-8 text-(--muted-foreground)" viewBox="0 0 24 24" fill="currentColor"
                                role="presentation">
                                <path fill="none" d="M0 0h24v24H0V0z"></path>
                                <path
                                    d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-10.6-3.47l1.63 2.18 2.58-3.22a.5.5 0 0 1 .78 0l2.96 3.7c.26.33.03.81-.39.81H9a.5.5 0 0 1-.4-.8l2-2.67c.2-.26.6-.26.8 0zM2 7v13c0 1.1.9 2 2 2h13c.55 0 1-.45 1-1s-.45-1-1-1H5c-.55 0-1-.45-1-1V7c0-.55-.45-1-1-1s-1 .45-1 1z">
                                </path>
                            </svg>
                            <span x-text="`${(title.images?.results?.length ?? 0)} PHOTOS`"
                                class="text-sm font-semibold uppercase"></span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-4 lg:gap-8">
                    <div>
                        <!-- Mobile only: small poster + genres + overview side by side -->
                        <div class="sm:hidden flex gap-3 mb-4">
                            <div class="shrink-0 w-28 aspect-2/3 rounded-lg overflow-hidden bg-(--muted)">
                                <img :src="`https://image.tmdb.org/t/p/w92${title.poster_path}`" :alt="title.title"
                                    class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap gap-1.5 mb-2">
                                    <template x-for="genre in (title.genres ?? [])" :key="genre.id">
                                        <span class="px-2 py-0.5 text-xs rounded-full border border-(--border)"
                                            x-text="genre.name"></span>
                                    </template>
                                </div>
                                <p class="text-sm text-(--muted-foreground) line-clamp-5" x-text="title.overview"></p>
                            </div>
                        </div>

                        <!-- Tablet+ genres -->
                        <div class="hidden sm:flex flex-wrap gap-2 mb-3">
                            <template x-for="genre in (title.genres ?? [])" :key="genre.id">
                                <span class="px-3 py-1 text-sm rounded-full border border-(--border) select-none"
                                    x-text="genre.name"></span>
                            </template>
                        </div>

                        <!-- Tablet+ overview -->
                        <div class="hidden sm:block mb-4 text-[15px] text-(--muted-foreground) leading-relaxed">
                            <p x-text="title.overview">
                            </p>
                        </div>

                        <div class="divide-y divide-(--border) border-y border-(--border)">
                            <div class="lg:hidden py-3">
                                <x-titles.stats />
                            </div>

                            <!-- Creators -->
                            <template x-if="title.created_by?.length">
                                <div class="flex items-baseline gap-3 sm:gap-4 py-3">
                                    <dt x-text="'Creator' + ((title.created_by?.length > 1) ? 's' : '')"
                                        class="shrink-0 font-bold text-sm sm:text-base w-14 sm:w-16"></dt>
                                    <dd class="flex flex-wrap items-center gap-x-1.5 gap-y-2 text-sm sm:text-base">
                                        <template x-for="(person, i) in title.created_by" :key="person.id">
                                            <span>
                                                <a href="#"
                                                    class="text-blue-500 dark:text-blue-400 hover:underline font-medium"
                                                    x-text="person.name"></a>
                                                <template x-if="i !== title.created_by.length - 1">
                                                    <span
                                                        class="font-bold text-(--muted-foreground)">&nbsp;&middot;&nbsp;</span>
                                                </template>
                                            </span>
                                        </template>
                                    </dd>
                                </div>
                            </template>

                            <!-- Stars -->
                            <div class="flex items-baseline gap-3 sm:gap-4 py-3">
                                <dt class="shrink-0 font-bold text-sm sm:text-base w-14 sm:w-16">Stars</dt>
                                <dd class="flex flex-wrap items-center gap-x-1.5 gap-y-2 text-sm sm:text-base">
                                    <template x-for="(person, i) in (title.credits?.cast ?? []).slice(0, 3)"
                                        :key="person.id">
                                        <span>
                                            <a href="#"
                                                class="text-blue-500 dark:text-blue-400 hover:underline font-medium"
                                                x-text="person.name"></a>
                                            <template x-if="i !== (title.credits?.cast ?? []).slice(0, 3).length - 1">
                                                <span
                                                    class="font-bold text-(--muted-foreground)">&nbsp;&middot;&nbsp;</span>
                                            </template>
                                        </span>
                                    </template>
                                </dd>
                                <div class="ml-auto">
                                    <a href="#"
                                        class="text-(--muted-foreground) dark:text-(--muted-foreground)">
                                        <x-lucide-chevron-right class="size-5" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-64 lg:row-span-2 flex flex-col gap-3">
                        <button type="button"
                            class="bg-(--primary) text-(--primary-foreground) border-(--primary) shadow-[inset_0_1px_0_oklch(1_0_0/20%),inset_0_-1px_0_oklch(0_0_0/15%)] hover:opacity-90 focus:opacity-90 flex items-center justify-center gap-2 border px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 outline-none select-none cursor-pointer active:not-aria-[haspopup]:translate-y-px w-full">
                            <x-lucide-bookmark class="size-4" stroke-width="2" />
                            <span>Add to Watchlist</span>
                        </button>

                        <div class="flex gap-2 text-sm pt-1">
                            <a :href="`https://www.imdb.com/title/${title.imdb_id}/reviews`" target="_blank"
                                class="text-blue-500 dark:text-blue-400 font-medium hover:underline"
                                x-text="`${title.vote_count?.toLocaleString()} User reviews`"></a>
                        </div>

                        <p x-show="title.tagline" class="text-sm italic text-(--muted-foreground)"
                            x-text="`&ldquo;${title.tagline}&rdquo;`"></p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                            <a :href="`https://streamex.sh/watch/tv/${title.id}`" target="_blank" class="contents">
                                <x-ui.button variant="secondary" size="lg"
                                    class="rounded-full! cursor-pointer!">
                                    <span>Watch on <strong>StreameX</strong></span>
                                </x-ui.button>
                            </a>

                            <a :data-title="title.title || title.name" x-data="telegram"
                                x-on:click.prevent="share" class="contents">
                                <x-ui.button variant="secondary" size="lg"
                                    class="rounded-full! cursor-pointer!">
                                    <span>Share on <strong>Telegram</strong></span>
                                </x-ui.button>
                            </a>

                            <template x-if="title.imdb_id">
                                <a :href="`https://www.imdb.com/title/${title.imdb_id}`" target="_blank"
                                    class="contents">
                                    <x-ui.button variant="secondary" size="lg"
                                        class="rounded-full! cursor-pointer!">
                                        <span>View on <strong>IMDB</strong></span>
                                    </x-ui.button>
                                </a>
                            </template>

                            <a :href="`https://www.themoviedb.org/tv/${title.id}`" target="_blank" class="contents">
                                <x-ui.button variant="secondary" size="lg"
                                    class="rounded-full! cursor-pointer!">
                                    <span>View on <strong>TMDB</strong></span>
                                </x-ui.button>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h2 class="text-2xl sm:text-2xl text-(--foreground) font-semibold tracking-wide">
                            Cast
                        </h2>

                        <div x-data="{ showAll: false }" class="hidden md:block">
                            <div class="grid grid-cols-2 gap-6">
                                <template
                                    x-for="(person, index) in (showAll ? (title.credits?.cast ?? []) : (title.credits?.cast?.slice(0,10) ?? []))"
                                    :key="index">
                                    <div class="flex items-center gap-3">
                                        <div class="size-22 bg-(--muted) rounded-full overflow-hidden shrink-0">
                                            <img :src="person.profile_path ?
                                                `https://image.tmdb.org/t/p/w185${person.profile_path}` :
                                                `https://ui-avatars.com/api/?name=${encodeURIComponent(person.name)}&background=random`"
                                                :alt="person.name" class="w-full h-full object-cover">
                                        </div>

                                        <div class="flex flex-col">
                                            <div x-text="person.name"
                                                class="whitespace-nowrap text-[15px] font-medium">
                                            </div>
                                            <div x-text="person.character"
                                                class="text-sm font-medium text-(--muted-foreground)">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- View More Button -->
                            <x-ui.button @click="showAll = true"
                                x-show="!showAll && (title.credits?.cast?.length > 12)" variant="link"
                                class="mt-4 text-blue-500! dark:text-blue-400!">
                                <span
                                    x-text="`View all cast (${Math.max((title.credits?.cast?.length ?? 0) - 12, 0)})`"></span>
                            </x-ui.button>
                        </div>

                        <x-ui.horizontal-slider class="md:hidden">
                            <template x-for="(person, index) in (title.credits?.cast ?? [])" :key="index">
                                <div class="flex flex-col items-center">
                                    <a :href="`/person/${person.id}`"
                                        class="block size-28 bg-(--muted) rounded-full overflow-hidden shrink-0 mb-2">
                                        <img :src="person.profile_path ?
                                            `https://image.tmdb.org/t/p/w185${person.profile_path}` :
                                            `https://ui-avatars.com/api/?name=${encodeURIComponent(person.name)}&background=random`"
                                            :alt="person.name" class="w-full h-full object-cover">
                                    </a>
                                    <a :href="`/person/${person.id}`" x-text="person.name"
                                        class="text-center whitespace-nowrap text-[15px] font-medium hover:underline"></a>
                                    <div x-text="person.character"
                                        class="text-center text-sm font-medium text-(--muted-foreground) line-clamp-2">
                                    </div>
                                </div>
                            </template>
                        </x-ui.horizontal-slider>

                        <dl class="divide-y divide-(--border) border-y border-(--border)">
                            <!-- Creators -->
                            <template x-if="title.created_by?.length">
                                <div class="flex items-baseline gap-3 sm:gap-4 py-3">
                                    <dt x-text="'Creator' + ((title.created_by?.length > 1) ? 's' : '')"
                                        class="shrink-0 font-bold text-sm sm:text-base w-14 sm:w-16"></dt>
                                    <dd class="flex flex-wrap items-center gap-x-1.5 gap-y-2 text-sm sm:text-base">
                                        <template x-for="(person, i) in title.created_by" :key="person.id">
                                            <span>
                                                <a href="#"
                                                    class="text-blue-500 dark:text-blue-400 hover:underline font-medium"
                                                    x-text="person.name"></a>
                                                <template x-if="i !== title.created_by.length - 1">
                                                    <span
                                                        class="font-bold text-(--muted-foreground)">&nbsp;&middot;&nbsp;</span>
                                                </template>
                                            </span>
                                        </template>
                                    </dd>
                                </div>
                            </template>

                            <!-- View all -->
                            <a href="#" class="flex items-center justify-between gap-3 sm:gap-4 py-3 group">
                                <span
                                    class="shrink-0 font-bold text-sm sm:text-base w-14 sm:w-16 whitespace-nowrap block">
                                    All cast & crew
                                </span>
                                <x-lucide-chevron-right
                                    class="size-5 sm:size-6 text-(--muted-foreground) group-hover:text-(--foreground)" />
                            </a>
                        </dl>
                    </div>

                    <div class="space-y-6 col-span-full">
                        <h2 class="text-2xl sm:text-2xl text-(--foreground) font-semibold tracking-wide">
                            Recommendations
                        </h2>

                        <x-titles.recommendations />
                    </div>

                    <div class="space-y-6 col-span-full">
                        <h2 class="text-2xl sm:text-2xl text-(--foreground) font-semibold tracking-wide">
                            Similar
                        </h2>

                        <x-titles.similar />
                    </div>
                </div>
            </div>
        </main>
        <x-footer />
    </div>

    @push('head')
        @vite('resources/js/pages/tv.js')
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
