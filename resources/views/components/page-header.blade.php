{{-- Encabezado de cada módulo: ruta, título, descripción y botones de acción. --}}
@props(['title', 'description' => null, 'section' => 'Panel de administración'])

<div class="space-y-4">
    <nav class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500" aria-label="Ruta">
        <span>{{ $section }}</span>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-medium text-slate-800">{{ $title }}</span>
    </nav>

    <header class="flex flex-col gap-4 border-b-2 border-slate-800/80 pb-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ $section }}</p>
            <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">{{ $title }}</h1>
            @if ($description)
                <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
        @endisset
    </header>
</div>
