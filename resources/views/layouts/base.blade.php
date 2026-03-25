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

<body class="antialiased bg-(--background) text-(--foreground)">
    <!-- Page Content -->
    <div class="min-h-screen">{{ $slot }}</div>

    <!-- Extra scripts -->
    @stack('scripts')
</body>

</html>
