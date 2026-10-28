<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Estudiante" title="Explorar ofertas" description="Ofertas de práctica abiertas para postulación." />

        <x-flash />

        <form method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row">
            <input type="search" name="q" value="{{ $q }}" placeholder="Buscar por título, área o empresa" class="field-input sm:max-w-sm">
            <select name="modalidad" class="field-input sm:max-w-[12rem]" onchange="this.form.submit()">
                <option value="">Todas las modalidades</option>
                @foreach(\App\Models\Oferta::MODALIDADES as $k => $label)
                    <option value="{{ $k }}" @selected($modalidad === $k)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Buscar</button>
        </form>

        @if($ofertas->isEmpty())
            <div class="rounded-xl bg-white px-6 py-14 text-center text-sm text-gray-500 shadow-sm ring-1 ring-gray-200">
                No se encontraron ofertas abiertas.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($ofertas as $oferta)
                    <article class="flex flex-col rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-semibold text-gray-900">{{ $oferta->titulo }}</h2>
                            @if(in_array($oferta->id, $postuladas, true))
                                <x-badge value="aceptada" label="Postulado" />
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-500">{{ $oferta->empresa?->razon_social }}</p>
                        <p class="mt-3 line-clamp-3 text-sm text-gray-600">{{ $oferta->descripcion }}</p>

                        <dl class="mt-4 grid grid-cols-3 gap-2 text-xs text-gray-500">
                            <div><dt>Modalidad</dt><dd class="font-medium text-gray-800">{{ \App\Models\Oferta::MODALIDADES[$oferta->modalidad] ?? $oferta->modalidad }}</dd></div>
                            <div><dt>Cupos</dt><dd class="font-medium text-gray-800">{{ $oferta->cupos }}</dd></div>
                            <div><dt>Horas</dt><dd class="font-medium text-gray-800">{{ $oferta->horas }}</dd></div>
                        </dl>

                        <a href="{{ route('estudiante.ofertas.show', $oferta->id) }}" class="btn-secondary mt-5">Ver detalle</a>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">{{ $ofertas->links() }}</div>
        @endif
    </div>
</x-app-layout>
