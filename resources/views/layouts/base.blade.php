<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <!-- SEO -->
    @stack('seo')

    <!-- Prevents theme flash on load -->
    <script>
        (() => {
            const m = matchMedia('(prefers-color-scheme: dark)');
            const t = localStorage.theme || 'system';
            const cls = t === 'system' ? (m.matches ? 'dark' : 'light') : t;
            document.documentElement.className = cls;
            document.documentElement.dataset.theme = cls;
        })();
    </script>

    <!-- Head -->
    @stack('head')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Extra styles -->
    @stack('styles')
</head>

<body class="antialiased bg-(--background) text-(--foreground)" data-user-id="@json(auth()->id())">
    <!-- Page Content -->
    <div class="min-h-screen">{{ $slot }}</div>

    <!-- Sonnar -->
    <div x-data @toast.window="$store.toast.add($event.detail)"
        class="fixed top-6 right-6 flex flex-col gap-2.5 w-full max-w-89 z-100">
        <template x-for="toast in $store.toast.items" :key="toast.id">
            <div x-transition
                class="bg-(--popover) text-(--popover-foreground) rounded-lg py-3 px-4 shadow-md ring-1 ring-(--border)">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i
                            :class="{
                                'fa-regular fa-bell text-(--muted-foreground)': toast.type === 'default',
                                'fa-solid fa-info-circle text-blue-500 dark:text-blue-400': toast.type === 'info',
                                'fa-solid fa-check-circle text-green-600 dark:text-green-400': toast
                                    .type === 'success',
                                'fa-solid fa-exclamation-triangle text-amber-500 dark:text-amber-400': toast
                                    .type === 'warning',
                                'fa-solid fa-times-circle text-red-400': toast.type === 'error',
                            }"></i>

                        <div x-text="toast.title" class="text-sm font-medium"></div>
                    </div>

                    <!-- Close button -->
                    <button @click="$store.toast.remove(toast.id)" class="text-xs opacity-70 hover:opacity-100">
                        ✕
                    </button>
                </div>

                <template x-if="toast.description">
                    <div x-text="toast.description" class="text-sm text-(--muted-foreground) mt-1"></div>
                </template>
            </div>
        </template>
    </div>

    <!-- Extra scripts -->
    @stack('scripts')
</body>

</html>
