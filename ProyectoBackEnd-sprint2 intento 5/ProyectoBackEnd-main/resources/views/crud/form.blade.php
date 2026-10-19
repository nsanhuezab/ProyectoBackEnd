<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <x-page-header :eyebrow="$labels['eyebrow']" :title="ucfirst($title)" :description="$labels['description']">
            <a href="{{ route($base.'.index') }}" class="btn-secondary">
                <x-icon name="arrow-left" class="h-4 w-4" /> Volver
            </a>
        </x-page-header>

        <x-flash />

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <p class="font-semibold">No se pudo guardar {{ $labels['g'] === 'a' ? 'la' : 'el' }} {{ $labels['singular'] }}.</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $action }}" class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 sm:p-6">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
                @foreach($fields as $field)
                    @include('crud._field')
                @endforeach
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route($base.'.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">{{ $model ? 'Guardar cambios' : 'Guardar' }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
