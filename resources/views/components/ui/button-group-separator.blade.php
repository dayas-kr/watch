@php
    $classes = implode(' ', [
        'relative',
        'shrink-0',
        'bg-input',
        'self-stretch',
        'data-[orientation=vertical]:w-px',
        'data-[orientation=vertical]:self-stretch',
        'data-[orientation=vertical]:my-px',
        'data-[orientation=vertical]:h-auto',
        'data-[orientation=horizontal]:h-px',
        'data-[orientation=horizontal]:w-auto',
        'data-[orientation=horizontal]:mx-px',
    ]);
@endphp

<div role="none" data-slot="button-group-separator"
    {{ $attributes->merge(['data-orientation' => 'vertical', 'class' => $classes]) }}>
</div>
