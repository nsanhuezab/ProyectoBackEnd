<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Empresa" title="Dashboard" :description="$empresa?->razon_social ?? 'Panel de empresa'">
            @if($empresa)
                <a href="{{ route('empresa.ofertas.index') }}" class="btn-secondary">Mis ofertas</a>
                <a href="{{ route('empresa.ofertas.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Publicar oferta</a>
            @endif
        </x-page-header>

        <x-flash />

        @if(! $empresa)
            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Tu usuario aún no está asociado a una empresa. Contacta al administrador para poder publicar ofertas.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Ofertas publicadas" :value="$stats['ofertas']" icon="briefcase" :href="$empresa ? route('empresa.ofertas.index') : null" />
            <x-stat-card label="Ofertas abiertas" :value="$stats['abiertas']" icon="clipboard-check" />
            <x-stat-card label="Postulaciones recibidas" :value="$stats['postulaciones']" icon="document" />
            <x-stat-card label="Practicantes activos" :value="$stats['practicantes']" icon="users" />
        </div>

        <section class="mt-8 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="border-b border-gray-100 px-4 py-3 sm:px-5">
                <h2 class="font-semibold text-gray-900">Postulaciones recientes</h2>
            </div>

            @forelse($recientes as $p)
                <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-3 last:border-0 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-gray-900">{{ $p->estudiante?->user?->name }}</p>
                        <p class="truncate text-sm text-gray-500">{{ $p->estudiante?->carrera?->nombre }} · {{ $p->oferta?->titulo }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge :value="$p->estado" :label="\App\Models\Postulacion::ESTADOS[$p->estado]" />
                        @if($p->estado === 'pendiente')
                            @foreach(['aceptada' => ['Aceptar', 'bg-green-600 hover:bg-green-700'], 'rechazada' => ['Rechazar', 'bg-red-600 hover:bg-red-700']] as $estado => [$label, $color])
                                <form method="POST" action="{{ route('empresa.postulaciones.estado', $p->id) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado" value="{{ $estado }}">
                                    <button type="submit" class="btn btn-sm text-white {{ $color }}">{{ $label }}</button>
                                </form>
                            @endforeach
                        @endif
                    </div>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-gray-500">Aún no has recibido postulaciones.</p>
            @endforelse
        </section>
    </div>
</x-app-layout>
