@php
    $defaultSizeClasses = implode(' ', ['h-8', 'rounded-lg']);

    $smallSizeClasses = implode(' ', ['data-[size="sm"]:h-7', 'data-[size="sm"]:rounded-[min(var(--radius-md),12px)]']);

    $largeSizeClasses = implode(' ', ['data-[size="lg"]:h-9', 'data-[size="lg"]:rounded-lg']);

    $sizeClasses = implode(' ', [$defaultSizeClasses, $smallSizeClasses, $largeSizeClasses]);

    $baseClasses = implode(' ', [
        'border-(--input)',
        'dark:bg-(--input)/30',
        'has-[[data-slot=input-group-control]:focus-visible]:border-(--ring)',
        'has-[[data-slot=input-group-control]:focus-visible]:ring-(--ring)/50',
        'has-[[data-slot][aria-invalid=true]]:ring-(--destructive)/20',
        'has-[[data-slot][aria-invalid=true]]:border-(--destructive)',
        'dark:has-[[data-slot][aria-invalid=true]]:ring-(--destructive)/40',
        'has-disabled:bg-(--input)/50',
        'dark:has-disabled:bg-(--input)/80',
        'group/input-group',
        'relative',
        'flex',
        'w-full',
        'min-w-0',
        'items-center',
        'border',
        'transition-colors',
        'outline-none',
        'has-disabled:opacity-50',
        'has-[[data-slot=input-group-control]:focus-visible]:ring-[3px]',
        'has-[[data-slot][aria-invalid=true]]:ring-[3px]',
        'has-[>[data-align=block-end]]:h-auto',
        'has-[>[data-align=block-end]]:flex-col',
        'has-[>[data-align=block-start]]:h-auto',
        'has-[>[data-align=block-start]]:flex-col',
        'has-[>textarea]:h-auto',
        'has-[>[data-align=block-end]]:[&>input]:pt-3',
        'has-[>[data-align=block-start]]:[&>input]:pb-3',
        'has-[>[data-align=inline-end]]:[&>input]:pr-1.5',
        'has-[>[data-align=inline-start]]:[&>input]:pl-1.5',
        'in-data-[slot=combobox-content]:focus-within:border-inherit',
        'in-data-[slot=combobox-content]:focus-within:ring-0',
    ]);

    $classes = implode(' ', [$baseClasses, $sizeClasses]);
@endphp

<div data-slot="input-group" role="group" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
