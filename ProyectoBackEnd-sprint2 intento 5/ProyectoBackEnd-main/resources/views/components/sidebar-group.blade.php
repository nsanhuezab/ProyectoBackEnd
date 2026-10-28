@props(['label', 'icon' => null, 'open' => false])

<div x-data="{ open: @js((bool) $open) }">
    <button type="button" @click="open = ! open" :aria-expanded="open"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">
        @if($icon)
            <x-icon :name="$icon" class="h-5 w-5 shrink-0 text-gray-400" />
        @endif
        <span class="flex-1 leading-snug">{{ $label }}</span>
        <span class="shrink-0 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''">
            <x-icon name="chevron-down" class="h-4 w-4" />
        </span>
    </button>
    <div x-show="open" x-cloak class="mt-0.5 space-y-0.5">
        {{ $slot }}
    </div>
</div>
