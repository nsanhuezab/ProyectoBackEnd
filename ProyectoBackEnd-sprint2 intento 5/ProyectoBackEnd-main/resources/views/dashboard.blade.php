<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Panel de administración" title="Dashboard" description="Resumen general del sistema de gestión de prácticas." />

        <x-flash />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($modulos as [$label, $total, $route, $icon, $visible])
                @if($visible)
                    <x-stat-card :label="$label" :value="$total" :icon="$icon" :href="route($route)" />
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>
