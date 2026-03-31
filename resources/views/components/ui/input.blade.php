@php
    $defaultSizeClasses = implode(' ', ['h-8', 'px-2.5', 'py-1', 'rounded-lg', 'text-base', 'md:text-sm']);

    $smallSizeClasses = implode(' ', [
        'data-[size="sm"]:h-7',
        'data-[size="sm"]:px-2.5',
        'data-[size="sm"]:py-0.5',
        'data-[size="sm"]:text-sm',
    ]);

    $largeSizeClasses = implode(' ', [
        'data-[size="lg"]:h-9',
        'data-[size="lg"]:px-3',
        'data-[size="lg"]:py-1.5',
        'data-[size="lg"]:text-[15px]',
    ]);

    $sizeClasses = implode(' ', [$defaultSizeClasses, $smallSizeClasses, $largeSizeClasses]);

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
        'file:text-(--foreground)',
        'placeholder:text-(--muted-foreground)',
        'selection:bg-(--primary)',
        'selection:text-(--primary-foreground)',
        'w-full',
        'min-w-0',
        'border',
        'bg-transparent',
        'transition-colors',
        'outline-none',
        'file:inline-flex',
        'file:h-6',
        'file:border-0',
        'file:bg-transparent',
        'file:text-sm',
        'file:font-medium',
        'focus-visible:ring-[3px]',
        'disabled:pointer-events-none',
        'disabled:cursor-not-allowed',
        'disabled:opacity-50',
        'aria-invalid:ring-[3px]',
    ]);

    $classes = implode(' ', [$baseClasses, $sizeClasses]);
@endphp

<input data-slot="input" {{ $attributes->merge(['type' => 'text', 'class' => $classes]) }}>
