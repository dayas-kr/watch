@php
    // Size classes driven by parent group/input-group's data-size attribute
$defaultSizeClasses = implode(' ', ['h-8', 'px-2.5', 'py-1', 'text-base', 'md:text-sm']);

$smallSizeClasses = implode(' ', [
    'group-data-[size="sm"]/input-group:h-7',
    'group-data-[size="sm"]/input-group:px-2.5',
    'group-data-[size="sm"]/input-group:py-0.5',
    'group-data-[size="sm"]/input-group:text-[0.8rem]',
]);

$largeSizeClasses = implode(' ', [
    'group-data-[size="lg"]/input-group:h-9',
    'group-data-[size="lg"]/input-group:px-3',
    'group-data-[size="lg"]/input-group:py-1.5',
    'group-data-[size="lg"]/input-group:text-sm',
]);

$sizeClasses = implode(' ', [$defaultSizeClasses, $smallSizeClasses, $largeSizeClasses]);

$baseClasses = implode(' ', [
    'border-(--input)',
    'focus-visible:border-(--ring)',
    'focus-visible:ring-(--ring)/50',
    'aria-invalid:ring-(--destructive)/20',
    'dark:aria-invalid:ring-(--destructive)/40',
    'aria-invalid:border-(--destructive)',
    'dark:aria-invalid:border-(--destructive)/50',
    'file:text-(--foreground)',
    'placeholder:text-(--muted-foreground)',
    'selection:bg-(--primary)',
    'selection:text-(--primary-foreground)',
    'w-full',
    'min-w-0',
    'transition-colors',
    'outline-none',
    'file:inline-flex',
    'file:h-6',
    'file:border-0',
    'file:bg-transparent',
    'file:text-sm',
    'file:font-medium',
    'disabled:pointer-events-none',
    'disabled:cursor-not-allowed',
    'disabled:opacity-50',
    'flex-1',
    'rounded-none',
    'border-0',
    'bg-transparent',
    'shadow-none',
    'ring-0',
    'focus-visible:ring-0',
    'disabled:bg-transparent',
    'aria-invalid:ring-0',
    'dark:bg-transparent',
    'dark:disabled:bg-transparent',
]);

$classes = implode(' ', [$baseClasses, $sizeClasses]);
@endphp

<input data-slot="input-group-control" {{ $attributes->merge(['class' => $classes]) }}>
