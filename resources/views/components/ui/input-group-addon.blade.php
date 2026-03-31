@php
    $classes = implode(' ', [
        // INLINE START — default
        'data-[align="inline-start"]:justify-center',
        'data-[align="inline-start"]:pl-2',
        'data-[align="inline-start"]:has-[>button]:ml-[-0.3rem]',
        'data-[align="inline-start"]:has-[>kbd]:ml-[-0.15rem]',
        'data-[align="inline-start"]:order-first',

        // INLINE START — sm
        'data-[align="inline-start"]:group-data-[size="sm"]/input-group:pl-1.5',
        'data-[align="inline-start"]:group-data-[size="sm"]/input-group:has-[>button]:ml-[-0.25rem]',
        'data-[align="inline-start"]:group-data-[size="sm"]/input-group:has-[>kbd]:ml-[-0.1rem]',

        // INLINE START — lg
        'data-[align="inline-start"]:group-data-[size="lg"]/input-group:pl-2.5',
        'data-[align="inline-start"]:group-data-[size="lg"]/input-group:has-[>button]:ml-[-0.35rem]',
        'data-[align="inline-start"]:group-data-[size="lg"]/input-group:has-[>kbd]:ml-[-0.2rem]',

        // INLINE END — default
        'data-[align="inline-end"]:justify-center',
        'data-[align="inline-end"]:pr-2',
        'data-[align="inline-end"]:has-[>button]:mr-[-0.3rem]',
        'data-[align="inline-end"]:has-[>kbd]:mr-[-0.15rem]',
        'data-[align="inline-end"]:order-last',

        // INLINE END — sm
        'data-[align="inline-end"]:group-data-[size="sm"]/input-group:pr-1.5',
        'data-[align="inline-end"]:group-data-[size="sm"]/input-group:has-[>button]:mr-[-0.25rem]',
        'data-[align="inline-end"]:group-data-[size="sm"]/input-group:has-[>kbd]:mr-[-0.1rem]',

        // INLINE END — lg
        'data-[align="inline-end"]:group-data-[size="lg"]/input-group:pr-2.5',
        'data-[align="inline-end"]:group-data-[size="lg"]/input-group:has-[>button]:mr-[-0.35rem]',
        'data-[align="inline-end"]:group-data-[size="lg"]/input-group:has-[>kbd]:mr-[-0.2rem]',

        // BLOCK START — default
        'data-[align="block-start"]:px-2.5',
        'data-[align="block-start"]:pt-2',
        'data-[align="block-start"]:group-has-[>input]/input-group:pt-2',
        'data-[align="block-start"]:[.border-b]:pb-2',
        'data-[align="block-start"]:order-first',
        'data-[align="block-start"]:w-full',
        'data-[align="block-start"]:justify-start',

        // BLOCK START — sm
        'data-[align="block-start"]:group-data-[size="sm"]/input-group:px-2.5',
        'data-[align="block-start"]:group-data-[size="sm"]/input-group:pt-1.5',
        'data-[align="block-start"]:group-data-[size="sm"]/input-group:group-has-[>input]/input-group:pt-1.5',

        // BLOCK START — lg
        'data-[align="block-start"]:group-data-[size="lg"]/input-group:px-3',
        'data-[align="block-start"]:group-data-[size="lg"]/input-group:pt-2.5',
        'data-[align="block-start"]:group-data-[size="lg"]/input-group:group-has-[>input]/input-group:pt-2.5',

        // BLOCK END — default
        'data-[align="block-end"]:px-2.5',
        'data-[align="block-end"]:pb-2',
        'data-[align="block-end"]:group-has-[>input]/input-group:pb-2',
        'data-[align="block-end"]:[.border-t]:pt-2',
        'data-[align="block-end"]:order-last',
        'data-[align="block-end"]:w-full',
        'data-[align="block-end"]:justify-start',

        // BLOCK END — sm
        'data-[align="block-end"]:group-data-[size="sm"]/input-group:px-2.5',
        'data-[align="block-end"]:group-data-[size="sm"]/input-group:pb-1.5',
        'data-[align="block-end"]:group-data-[size="sm"]/input-group:group-has-[>input]/input-group:pb-1.5',

        // BLOCK END — lg
        'data-[align="block-end"]:group-data-[size="lg"]/input-group:px-3',
        'data-[align="block-end"]:group-data-[size="lg"]/input-group:pb-2.5',
        'data-[align="block-end"]:group-data-[size="lg"]/input-group:group-has-[>input]/input-group:pb-2.5',

        // BASE
        'text-(--muted-foreground)',
        'h-auto',
        'gap-2',
        'py-1.5',
        'text-sm',
        'font-medium',
        'group-data-[disabled=true]/input-group:opacity-50',
        '[&>kbd]:rounded-[calc(var(--radius)-5px)]',
        '[&>svg:not([class*="size-"])]:size-4',
        'flex',
        'cursor-text',
        'items-center',
        'select-none',
    ]);
@endphp

<div role="group" data-slot="input-group-addon"
    {{ $attributes->merge(['data-align' => 'inline-start', 'class' => $classes]) }}>
    {{ $slot }}
</div>
