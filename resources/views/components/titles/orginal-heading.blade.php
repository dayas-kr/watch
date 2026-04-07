<template x-if="title.name !== title.original_name || title.title !== title.original_title">
    <span x-text="`Original title: ${title.original_name || title.original_title}`"
        {{ $attributes->merge(['class' => 'text-sm font-medium text-(--muted-foreground)']) }}></span>
</template>
