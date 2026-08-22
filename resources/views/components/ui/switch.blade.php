@php
    $switchCls = implode(' ', [
        'h-[1.15rem] w-8 shrink-0 inline-flex items-center rounded-full shadow-xs transition-all outline-none cursor-default',
        'peer-checked:bg-(--primary)',
        'bg-(--input)',
        'border border-transparent',
        'dark:bg-(--input)/80',
        'peer-focus-visible:ring-[3px] peer-focus-visible:border-(--ring) peer-focus-visible:ring-(--ring)/50',
        'peer-focus-visible:peer-checked:border-(--primary)',
        'peer-disabled:cursor-not-allowed peer-disabled:opacity-50',
        'dark:peer-checked:[&>.switch-thumb]:bg-(--primary-foreground)',
        'peer-checked:[&>.switch-thumb]:translate-x-[calc(100%-2px)]',
    ]);

    $switchThumbCls = implode(' ', [
        'switch-thumb',
        'block size-4 rounded-full ring-0 transition-transform pointer-events-none',
        'bg-(--background) dark:bg-(--foreground)',
        'translate-x-0',
    ]);
@endphp

<label class="relative inline-flex items-center select-none">
    <input type="checkbox" role="switch" {{ $attributes->merge(['class' => 'peer sr-only']) }} />

    <div class="{{ $switchCls }}">
        <span class="{{ $switchThumbCls }}"></span>
    </div>
</label>
