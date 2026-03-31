<span data-slot="input-group-text"
    {{ $attributes->merge(['class' => 'text-(--muted-foreground) flex items-center gap-2 text-sm [&_svg]:pointer-events-none [&_svg:not([class*="size-"])]:size-4']) }}>
    {{ $slot }}
</span>
