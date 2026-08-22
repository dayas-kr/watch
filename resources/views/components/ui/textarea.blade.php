@php
    $baseClasses = implode(' ', [
        'dark:bg-(--input)/30',
        'border-(--input)',
        'focus-visible:border-(--ring)',
        'focus-visible:ring-(--ring)/50',
        'aria-invalid:ring-(--destructive)/20',
        'dark:aria-invalid:ring-(--destructive)/40',
        'aria-invalid:border-(--destructive)',
        'dark:aria-invalid:border-(--destructive)/50',
        'disabled:bg-(--input)/50',
        'dark:disabled:bg-(--input)/80',
        'placeholder:text-(--muted-foreground)',
        'selection:bg-(--primary)',
        'selection:text-(--primary-foreground)',
        'w-full',
        'min-w-0',
        'border',
        'bg-transparent',
        'px-2.5',
        'py-2',
        'rounded-lg',
        'text-base',
        'md:text-sm',
        'transition-colors',
        'outline-none',
        'focus-visible:ring-[3px]',
        'disabled:pointer-events-none',
        'disabled:cursor-not-allowed',
        'disabled:opacity-50',
        'aria-invalid:ring-[3px]',
    ]);
@endphp

<textarea data-slot="textarea" {{ $attributes->merge([
    'rows' => 3,
    'class' => $baseClasses,
]) }}>{{ $slot }}</textarea>
