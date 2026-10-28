<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Estudiante" title="Seguimiento" description="Avance de tu práctica y evaluaciones recibidas.">
            @if($promedio)
                <span class="rounded-lg bg-ucsc-50 px-3 py-2 text-sm text-ucsc-800">Promedio: <strong>{{ number_format($promedio, 1, ',', '.') }}</strong></span>
            @endif
        </x-page-header>

        <x-flash />

        @forelse($practicas as $practica)
            <section class="mb-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $practica->empresa?->razon_social }}</h2>
                        <p class="text-sm text-gray-500">Supervisor: {{ $practica->supervisor }} · {{ $practica->horas_totales }} horas</p>
                    </div>
                    <x-badge :value="$practica->estado" :label="\App\Models\Practica::ESTADOS[$practica->estado]" />
                </div>

                <div class="mt-4">
                    <div class="mb-1 flex justify-between text-xs text-gray-500">
                        <span>{{ $practica->fecha_inicio->format('d/m/Y') }}</span>
                        <span>{{ $practica->progreso() }}%</span>
                        <span>{{ $practica->fecha_termino->format('d/m/Y') }}</span>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-ucsc-600" style="width: {{ $practica->progreso() }}%"></div>
                    </div>
                </div>

                <h3 class="mt-6 text-sm font-semibold uppercase tracking-wider text-gray-500">Evaluaciones</h3>
                @forelse($practica->evaluaciones as $ev)
                    <div class="mt-3 rounded-lg border border-gray-100 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-gray-900">{{ \App\Models\Evaluacion::TIPOS[$ev->tipo] ?? $ev->tipo }} <span class="font-normal text-gray-500">· {{ $ev->fecha->format('d/m/Y') }}</span></p>
                            <span class="text-lg font-bold text-ucsc-700">{{ number_format((float) $ev->nota, 1, ',', '.') }}</span>
                        </div>
                        @if($ev->comentario)
                            <p class="mt-1 text-sm text-gray-600">{{ $ev->comentario }}</p>
                        @endif
                    </div>
                @empty
                    <p class="mt-2 text-sm text-gray-500">Aún no tienes evaluaciones registradas.</p>
                @endforelse
            </section>
        @empty
            <div class="rounded-xl bg-white px-6 py-14 text-center text-sm text-gray-500 shadow-sm ring-1 ring-gray-200">
                {{ $estudiante ? 'Aún no tienes una práctica asignada.' : 'Tu perfil de estudiante aún no está registrado. Contacta a coordinación.' }}
            </div>
        @endforelse
    </div>
</x-app-layout>
