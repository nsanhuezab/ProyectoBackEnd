<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Estudiante" title="Mis postulaciones" description="Seguimiento de las ofertas a las que has postulado.">
            <a href="{{ route('estudiante.ofertas.index') }}" class="btn-primary">Explorar ofertas</a>
        </x-page-header>

        <x-flash />

        @if(! $postulaciones || $postulaciones->isEmpty())
            <div class="rounded-xl bg-white px-6 py-14 text-center text-sm text-gray-500 shadow-sm ring-1 ring-gray-200">
                Aún no tienes postulaciones.
            </div>
        @else
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <ul class="divide-y divide-gray-100">
                    @foreach($postulaciones as $p)
                        <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $p->oferta?->titulo }}</p>
                                <p class="truncate text-sm text-gray-500">{{ $p->oferta?->empresa?->razon_social }} · postulada el {{ $p->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-badge :value="$p->estado" :label="\App\Models\Postulacion::ESTADOS[$p->estado]" />
                                @if($p->estado === 'pendiente')
                                    <form method="POST" action="{{ route('estudiante.postulaciones.retirar', $p->id) }}" onsubmit="return confirm('¿Retirar esta postulación?');">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm bg-white text-red-600 ring-1 ring-inset ring-red-200 hover:bg-red-50">Retirar</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if($postulaciones->hasPages())
                    <div class="border-t border-gray-100 px-4 py-3">{{ $postulaciones->links() }}</div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
