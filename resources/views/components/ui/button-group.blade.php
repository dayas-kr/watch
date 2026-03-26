@php
    $classes = implode(' ', [
        'flex',
        'w-fit',
        'items-stretch',
        '*:focus-visible:z-10',
        '*:focus-visible:relative',
        '[&>*:not(:first-child)]:rounded-l-none',
        '[&>*:not(:last-child)]:rounded-r-none',
        '[&>[data-slot]:not(:has(~[data-slot]))]:rounded-r-lg!',
        '[&>*:not(:first-child)]:border-l-0',
        "[&>[data-slot=select-trigger]:not([class*='w-'])]:w-fit",
        '[&>input]:flex-1',
        'has-[>[data-slot=button-group]]:gap-2',
        'has-[select[aria-hidden=true]:last-child]:[&>[data-slot=select-trigger]:last-of-type]:rounded-r-lg',
    ]);
@endphp

<div role="group" data-slot="button-group" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
