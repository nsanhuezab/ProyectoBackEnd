<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Estudiante" :title="'Hola, '.explode(' ', auth()->user()->name)[0]" description="Tu proceso de práctica profesional en un solo lugar.">
            <a href="{{ route('estudiante.ofertas.index') }}" class="btn-primary"><x-icon name="search" class="h-4 w-4" /> Explorar ofertas</a>
        </x-page-header>

        <x-flash />

        @if(! $estudiante)
            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Tu perfil de estudiante aún no está registrado. Contacta a coordinación para poder postular.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Postulaciones" :value="$stats['total']" icon="document" :href="route('estudiante.postulaciones.index')" />
            <x-stat-card label="Pendientes" :value="$stats['pendientes']" icon="clipboard-check" />
            <x-stat-card label="Aceptadas" :value="$stats['aceptadas']" icon="star" />
            <x-stat-card label="Ofertas abiertas" :value="$stats['ofertas']" icon="briefcase" :href="route('estudiante.ofertas.index')" />
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 sm:p-5">
                <h2 class="font-semibold text-gray-900">Mi práctica</h2>
                @if($practica)
                    <p class="mt-2 text-gray-900">{{ $practica->empresa?->razon_social }}</p>
                    <p class="text-sm text-gray-500">Supervisor: {{ $practica->supervisor }}</p>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-ucsc-600" style="width: {{ $practica->progreso() }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $practica->progreso() }}%</span>
                    </div>
                    <a href="{{ route('estudiante.seguimiento') }}" class="mt-4 inline-block text-sm font-medium text-ucsc-700 hover:underline">Ver seguimiento</a>
                @else
                    <p class="mt-2 text-sm text-gray-500">Aún no tienes una práctica asignada.</p>
                @endif
            </section>

            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="border-b border-gray-100 px-4 py-3 sm:px-5">
                    <h2 class="font-semibold text-gray-900">Últimas postulaciones</h2>
                </div>
                @forelse($postulaciones as $p)
                    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 last:border-0 sm:px-5">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $p->oferta?->titulo }}</p>
                            <p class="truncate text-sm text-gray-500">{{ $p->oferta?->empresa?->razon_social }}</p>
                        </div>
                        <x-badge :value="$p->estado" :label="\App\Models\Postulacion::ESTADOS[$p->estado]" />
                    </div>
                @empty
                    <p class="px-5 py-10 text-center text-sm text-gray-500">Todavía no has postulado.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
