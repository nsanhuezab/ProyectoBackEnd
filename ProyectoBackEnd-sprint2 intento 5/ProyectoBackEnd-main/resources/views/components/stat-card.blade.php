@props(['label', 'value', 'icon' => 'squares', 'href' => null, 'hint' => null])

@php
    $tag = $href ? 'a' : 'div';
    $classes = 'flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 sm:p-5'.($href ? ' transition hover:ring-ucsc-300' : '');
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-ucsc-50 text-ucsc-700">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <span class="min-w-0">
        <span class="block text-2xl font-bold text-gray-900">{{ $value }}</span>
        <span class="block truncate text-sm text-gray-500">{{ $label }}</span>
        @if($hint)
            <span class="block text-xs text-gray-400">{{ $hint }}</span>
        @endif
    </span>
</{{ $tag }}>
