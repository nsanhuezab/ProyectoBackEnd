{{-- Errores de validación, solo dentro del modal que envió el formulario. --}}
@props(['modal'])

@if (old('_modal') === $modal && $errors->any())
    <div data-form-errors class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[12.5px] text-red-800" role="alert">
        <p class="font-bold mb-1">Revisa los siguientes campos:</p>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
