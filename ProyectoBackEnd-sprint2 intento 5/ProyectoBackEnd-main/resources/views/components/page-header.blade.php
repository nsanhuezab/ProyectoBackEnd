@props(['eyebrow' => null, 'title', 'description' => null])

<div class="mb-6 flex flex-col gap-4 border-b border-gray-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        @if($eyebrow)
            <p class="text-xs font-semibold uppercase tracking-widest text-ucsc-600">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-1 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $title }}</h1>
        @if($description)
            <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
        @endif
    </div>
    @if(trim((string) $slot) !== '')
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
