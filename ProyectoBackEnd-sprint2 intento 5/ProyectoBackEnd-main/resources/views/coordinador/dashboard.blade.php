<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Coordinador" title="Dashboard" description="Estado del proceso de prácticas profesionales.">
            <a href="{{ route('coordinador.gestion') }}" class="btn-primary">Ir a gestión</a>
        </x-page-header>

        <x-flash />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Prácticas en curso" :value="$stats['en_curso']" icon="clipboard-check" :href="route('gestion.practicas.index')" />
            <x-stat-card label="Postulaciones pendientes" :value="$stats['pendientes']" icon="document" :href="route('gestion.postulaciones.index')" />
            <x-stat-card label="Estudiantes sin práctica" :value="$stats['sin_practica']" icon="users" :href="route('admin.estudiantes.index')" />
            <x-stat-card label="Evaluaciones del mes" :value="$stats['evaluaciones_mes']" icon="star" :hint="$promedio ? 'Promedio general: '.number_format($promedio, 1, ',', '.') : null" :href="route('gestion.evaluaciones.index')" />
        </div>

        <div class="mt-8 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="border-b border-gray-100 px-4 py-3 sm:px-5">
                <h2 class="font-semibold text-gray-900">Prácticas por finalizar</h2>
            </div>

            @forelse($practicas as $p)
                <div class="flex flex-col gap-2 border-b border-gray-100 px-4 py-3 last:border-0 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-gray-900">{{ $p->estudiante?->user?->name }}</p>
                        <p class="truncate text-sm text-gray-500">{{ $p->empresa?->razon_social }} · termina {{ $p->fecha_termino->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex items-center gap-3 sm:w-56">
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-ucsc-600" style="width: {{ $p->progreso() }}%"></div>
                        </div>
                        <span class="w-10 text-right text-xs text-gray-500">{{ $p->progreso() }}%</span>
                    </div>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-gray-500">No hay prácticas en curso.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
