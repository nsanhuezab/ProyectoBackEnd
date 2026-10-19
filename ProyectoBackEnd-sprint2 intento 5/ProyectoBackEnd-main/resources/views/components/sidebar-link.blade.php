@props(['href', 'icon' => null, 'active' => false, 'sub' => null, 'child' => false])

@php
    $linkClass = 'group flex items-center gap-3 rounded-lg text-sm transition '
        .($child ? 'py-2 pl-11 pr-3 ' : 'px-3 py-2.5 ')
        .($active ? 'bg-ucsc-50 font-semibold text-ucsc-800' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900');
    $iconClass = 'h-5 w-5 shrink-0 '.($active ? 'text-ucsc-700' : 'text-gray-400 group-hover:text-gray-600');
@endphp

<a href="{{ $href }}" class="{{ $linkClass }}" @if($active) aria-current="page" @endif>
    @if($icon && ! $child)
        <x-icon :name="$icon" :class="$iconClass" />
    @endif
    <span class="min-w-0 leading-snug">
        {{ $slot }}
        @if($sub)
            <span class="block text-xs font-normal text-gray-400">{{ $sub }}</span>
        @endif
    </span>
</a>
