<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Coordinador" title="Gestión y evaluación de prácticas" description="Lo que requiere tu revisión ahora.">
            <a href="{{ route('gestion.practicas.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nueva práctica</a>
        </x-page-header>

        <x-flash />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Postulaciones pendientes --}}
            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 sm:px-5">
                    <h2 class="font-semibold text-gray-900">Postulaciones pendientes</h2>
                    <a href="{{ route('gestion.postulaciones.index') }}" class="text-sm font-medium text-ucsc-700 hover:underline">Ver todas</a>
                </div>

                @forelse($postulaciones as $p)
                    <div class="space-y-2 border-b border-gray-100 px-4 py-3 last:border-0 sm:px-5">
                        <div>
                            <p class="font-medium text-gray-900">{{ $p->estudiante?->user?->name }}</p>
                            <p class="text-sm text-gray-500">{{ $p->oferta?->titulo }} · {{ $p->oferta?->empresa?->razon_social }}</p>
                        </div>
                        <div class="flex gap-2">
                            @foreach(['aceptada' => ['Aceptar', 'bg-green-600 hover:bg-green-700'], 'rechazada' => ['Rechazar', 'bg-red-600 hover:bg-red-700']] as $estado => [$label, $color])
                                <form method="POST" action="{{ route('gestion.postulaciones.estado', $p) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado" value="{{ $estado }}">
                                    <button type="submit" class="btn btn-sm text-white {{ $color }}">{{ $label }}</button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-10 text-center text-sm text-gray-500">No hay postulaciones pendientes.</p>
                @endforelse
            </section>

            {{-- Prácticas sin evaluar --}}
            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 sm:px-5">
                    <h2 class="font-semibold text-gray-900">Prácticas sin evaluar</h2>
                    <a href="{{ route('gestion.evaluaciones.index') }}" class="text-sm font-medium text-ucsc-700 hover:underline">Ver evaluaciones</a>
                </div>

                @forelse($sinEvaluar as $p)
                    <div class="flex flex-col gap-2 border-b border-gray-100 px-4 py-3 last:border-0 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $p->estudiante?->user?->name }}</p>
                            <p class="truncate text-sm text-gray-500">{{ $p->empresa?->razon_social }}</p>
                        </div>
                        <a href="{{ route('gestion.evaluaciones.create', ['practica' => $p->id]) }}" class="btn-primary btn-sm self-start sm:self-auto">Evaluar</a>
                    </div>
                @empty
                    <p class="px-5 py-10 text-center text-sm text-gray-500">Todas las prácticas tienen evaluación.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
