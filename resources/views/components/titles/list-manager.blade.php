<div x-data="listManager" @list:add.window="addToList($event.detail)"
    @list:remove.window="removeFromList($event.detail)" @list:delete.window="deleteList($event.detail)"
    @list:clear.window="clearList($event.detail)" @list:open-dialog.window="openDialog($event.detail)"
    @list:close-dialog.window="closeDialog" @keydown.escape.window="closeDialog" @keydown.tab.prevent="closeDialog">
    <template x-teleport="body">
        <div x-show="open" x-trap.noScroll="open" @keydown.arrow-down.prevent="navigate(1)"
            @keydown.arrow-up.prevent="navigate(-1)" @keydown.enter.prevent="select()"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 isolate z-50 bg-black/40 dark:bg-black/70 supports-backdrop-filter:backdrop-blur-xs">

            <div @click.outside="closeDialog()" x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-[0.97] translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-[0.97] translate-y-1"
                class="bg-(--background) shadow-2xl shadow-black/10 dark:shadow-black/40 ring-neutral-200/80 dark:ring-neutral-800/80 fixed top-1/2 left-1/2 z-50 w-full max-w-[calc(100%-2rem)] -translate-x-1/2 -translate-y-1/2 rounded-2xl p-0 text-sm ring-1 outline-none sm:max-w-xl overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-(--border)/60">
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <h2 class="text-base font-semibold tracking-tight"
                                x-text="createListOption ? 'Create new list' : 'Save to list'"></h2>
                            <template x-if="title && !createListOption">
                                <p class="text-(--muted-foreground) text-[13px] leading-snug">
                                    <span class="text-(--foreground) font-medium"
                                        x-text="title.name || title.title"></span>
                                    <template x-if="title.release_date || title.first_air_date">
                                        <span
                                            x-text="`· ${dayjs(title.release_date || title.first_air_date).format('YYYY')}`"></span>
                                    </template>
                                </p>
                            </template>
                        </div>
                        <x-ui.button @click="closeDialog" variant="ghost" size="icon-sm"
                            class="shrink-0 -mt-0.5 -mr-1 rounded-lg opacity-60 hover:opacity-100 transition-opacity">
                            <x-lucide-x class="size-4" />
                        </x-ui.button>
                    </div>
                </div>

                <!-- Add to lists view -->
                <div x-show="!createListOption" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0" class="px-6 py-5 space-y-4">

                    <div class="flex items-center gap-2">
                        <x-ui.input-group class="flex-1">
                            <x-ui.input-group-addon class="text-(--muted-foreground)">
                                <x-lucide-search class="size-3.5" />
                            </x-ui.input-group-addon>
                            <x-ui.input-group-input x-ref="searchInput" x-model="search" placeholder="Search lists..."
                                @input="onSearchInput()" @click="onSearchClick($event)" />
                        </x-ui.input-group>
                        <x-ui.button variant="outline"
                            @click="createListOption = true; $nextTick(() => $refs.listName?.focus())"
                            class="shrink-0 gap-1.5">
                            <x-lucide-plus class="size-3.5" />
                            New list
                        </x-ui.button>
                    </div>

                    <div x-ref="listContainer"
                        class="flex flex-col gap-0.5 max-h-52 overflow-y-auto -mx-1 px-1 no-scrollbar">

                        <!-- Loading -->
                        <template x-if="loading">
                            <div class="flex items-center justify-center gap-2.5 py-8 text-(--muted-foreground)">
                                <x-lucide-loader-2 class="animate-spin size-4" />
                                <span class="text-[13px]">Loading your lists...</span>
                            </div>
                        </template>

                        <!-- Error -->
                        <template x-if="!loading && error">
                            <div class="flex flex-col items-center gap-3 py-8">
                                <div class="size-9 rounded-full bg-(--destructive)/10 flex items-center justify-center">
                                    <x-lucide-alert-circle class="size-4 text-(--destructive)" />
                                </div>
                                <div class="text-center space-y-1">
                                    <p class="text-[13px] text-(--foreground) font-medium">Something went wrong</p>
                                    <p class="text-(--destructive) text-xs" x-text="errorMessage"></p>
                                </div>
                                <button @click="fetch()"
                                    class="text-xs text-(--muted-foreground) hover:text-(--foreground) underline underline-offset-2 cursor-pointer transition-colors">
                                    Try again
                                </button>
                            </div>
                        </template>

                        <!-- Lists -->
                        <template x-if="!loading && !error">
                            <div class="flex flex-col">
                                <template x-for="(list, index) in filtered" :key="list.id">
                                    <div x-data="{
                                        list: list,
                                        inUserList: false,
                                        init() {
                                            this._checkInUserList(title);
                                        },
                                        _checkInUserList(t) {
                                            if (!t) return;
                                            const list_id = this.list.id;
                                            const { id: media_id, media_type } = t;
                                            const user_lists = Alpine.store('db')?.user_lists || [];
                                            const foundList = user_lists.find(l => l.id === list_id);
                                            this.inUserList = foundList?.items?.some(
                                                item => item.media_id === media_id && item.media_type === media_type
                                            ) ?? false;
                                        },
                                        updateListOption(data) {
                                            const { id, inUserList } = data;
                                            if (id === this.list.id) {
                                                this.inUserList = inUserList;
                                            }
                                        }
                                    }"
                                        @list:update-list-option.window="updateListOption($event.detail)"
                                        @list:title-changed.window="_checkInUserList($event.detail)"
                                        :data-index="index" @mouseenter="activeIndex = index"
                                        :data-active="activeIndex === index"
                                        class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-100 cursor-default
                                               data-[active=true]:bg-(--muted)">

                                        <!-- List icon -->
                                        <div
                                            class="shrink-0 size-8 rounded-lg bg-(--muted) flex items-center justify-center text-(--muted-foreground) group-data-[active=true]:bg-(--background) transition-colors">
                                            <x-lucide-list class="size-3.5" />
                                        </div>

                                        <!-- List info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-[13px] truncate" x-text="list.name"></p>
                                            <p class="text-(--muted-foreground) text-xs"
                                                x-text="`${list.item_count} item${list.item_count !== 1 ? 's' : ''}`">
                                            </p>
                                        </div>

                                        <!-- Add/Remove button -->
                                        <button @click.prevent="activeIndex = index; select(!inUserList)"
                                            class="shrink-0 h-7 px-3 rounded-lg text-xs font-medium transition-all duration-150 cursor-pointer border"
                                            :class="inUserList
                                                ?
                                                'bg-(--destructive)/8 border-(--destructive)/20 text-(--destructive) hover:bg-(--destructive)/15' :
                                                'bg-(--background) border-(--border) text-(--foreground) hover:bg-(--muted) group-data-[active=true]:border-(--input)'"
                                            x-text="inUserList ? 'Remove' : 'Add'">
                                        </button>
                                    </div>
                                </template>

                                <div x-show="initialized && filtered.length === 0"
                                    class="flex flex-col items-center gap-2 py-10">
                                    <div class="size-9 rounded-full bg-(--muted) flex items-center justify-center">
                                        <x-lucide-search class="size-4 text-(--muted-foreground)" />
                                    </div>
                                    <p class="text-(--muted-foreground) text-[13px]">No lists found</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Create list view -->
                <div x-show="createListOption" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0" class="px-6 py-5 space-y-4">

                    <div class="grid gap-3">
                        <x-ui.label for="list-name">Name</x-ui.label>
                        <x-ui.input id="list-name" x-model="createForm.name" x-ref="listName"
                            x-bind:disabled="loadingOnCreate" type="text" placeholder="My favourite films..."
                            autocomplete="off" />
                    </div>

                    <div class="grid gap-3">
                        <x-ui.label for="list-description">
                            <span>Description</span>
                            <span class="normal-case font-normal text-(--muted-foreground)">(optional)</span>
                        </x-ui.label>
                        <x-ui.textarea id="list-description" x-model="createForm.description"
                            x-bind:disabled="loadingOnCreate" rows="3"
                            placeholder="A short description of your list..." />
                    </div>

                    <div class="flex items-center justify-between py-0.5">
                        <div class="space-y-0.5">
                            <p class="text-[13px] font-medium">Public list</p>
                            <p class="text-(--muted-foreground) text-[13px]">Anyone can discover and view this list</p>
                        </div>
                        <x-ui.switch x-model="createForm.public" x-bind:disabled="loadingOnCreate" />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <x-ui.button variant="ghost" @click="createListOption = false"
                            x-bind:disabled="loadingOnCreate">
                            Back
                        </x-ui.button>
                        <x-ui.button @click="createList(createForm)"
                            x-bind:disabled="!createForm.name.trim() || loadingOnCreate">
                            <x-lucide-loader-circle x-show="loadingOnCreate" stroke-width="2.5"
                                class="animate-spin size-3.5" />
                            <span x-text="loadingOnCreate ? 'Creating...' : 'Create list'"></span>
                        </x-ui.button>
                    </div>
                </div>

            </div>
        </div>
    </template>
</div>
