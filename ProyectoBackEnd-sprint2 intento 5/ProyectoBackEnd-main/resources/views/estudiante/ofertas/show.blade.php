<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-header eyebrow="Explorar ofertas" :title="$oferta->titulo" :description="$oferta->empresa?->razon_social">
            <a href="{{ route('estudiante.ofertas.index') }}" class="btn-secondary"><x-icon name="arrow-left" class="h-4 w-4" /> Volver</a>
        </x-page-header>

        <x-flash />

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="space-y-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 sm:p-6">
            <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
                <div><dt class="text-gray-500">Modalidad</dt><dd class="font-medium">{{ \App\Models\Oferta::MODALIDADES[$oferta->modalidad] ?? $oferta->modalidad }}</dd></div>
                <div><dt class="text-gray-500">Cupos</dt><dd class="font-medium">{{ $oferta->cupos }}</dd></div>
                <div><dt class="text-gray-500">Horas</dt><dd class="font-medium">{{ $oferta->horas }}</dd></div>
                <div><dt class="text-gray-500">Cierre</dt><dd class="font-medium">{{ $oferta->fecha_limite?->format('d/m/Y') ?? 'Sin fecha' }}</dd></div>
            </dl>

            <div>
                <h2 class="font-semibold text-gray-900">Descripción</h2>
                <p class="mt-1 whitespace-pre-line text-sm text-gray-600">{{ $oferta->descripcion }}</p>
            </div>

            @if($oferta->requisitos)
                <div>
                    <h2 class="font-semibold text-gray-900">Requisitos</h2>
                    <p class="mt-1 whitespace-pre-line text-sm text-gray-600">{{ $oferta->requisitos }}</p>
                </div>
            @endif

            <div class="border-t border-gray-100 pt-5">
                @if($yaPostulo)
                    <p class="text-sm text-gray-600">Ya postulaste a esta oferta. Revisa su estado en <a class="font-medium text-ucsc-700 hover:underline" href="{{ route('estudiante.postulaciones.index') }}">Mis postulaciones</a>.</p>
                @elseif(! $estudiante)
                    <p class="text-sm text-amber-700">Tu perfil de estudiante aún no está registrado. Contacta a coordinación para poder postular.</p>
                @else
                    <form method="POST" action="{{ route('estudiante.ofertas.postular', $oferta->id) }}" class="space-y-3">
                        @csrf
                        <label for="mensaje" class="field-label">Mensaje para la empresa (opcional)</label>
                        <textarea id="mensaje" name="mensaje" rows="3" class="field-input">{{ old('mensaje') }}</textarea>
                        <button type="submit" class="btn-primary w-full sm:w-auto">Postular a esta oferta</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
