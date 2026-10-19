@props(['title'])

<div class="pt-5">
    <p class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-widest text-gray-400">{{ $title }}</p>
    <div class="space-y-0.5">
        {{ $slot }}
    </div>
</div>
