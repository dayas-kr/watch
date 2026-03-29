<div x-data="horizontalSlider" {{ $attributes->merge(['class' => 'relative']) }}>
    <!-- Scroll Left -->
    <button x-show="canScrollLeft" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" @click="scrollLeft()"
        class="absolute left-px top-1/2 -translate-y-1/2 z-10 border border-white/75 dark:border-white/50 shadow-sm backdrop-blur-xs px-px py-1.75 rounded text-white hover:text-amber-400"
        aria-label="Scroll left">
        <x-lucide-chevron-left class="size-9 stroke-3" />
    </button>

    <!-- Slider -->
    <div data-slider class="flex gap-4 overflow-x-auto no-scrollbar">
        {{ $slot }}
    </div>

    <!-- Scroll Right -->
    <button x-show="canScrollRight" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" @click="scrollRight()"
        class="absolute right-px top-1/2 -translate-y-1/2 z-10 border border-white/75 dark:border-white/50 shadow-sm backdrop-blur-xs px-px py-1.75 rounded text-white hover:text-amber-400"
        aria-label="Scroll right">
        <x-lucide-chevron-right class="size-9 stroke-3" />
    </button>
</div>
