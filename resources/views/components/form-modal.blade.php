{{--
    Modal con encabezado oscuro. Se abre con openModal('id') y se cierra con closeModal('id').
    Si el formulario de adentro falla en la validación, el layout lo vuelve a abrir solo.
--}}
@props(['id', 'title', 'subtitle' => null, 'width' => 'max-w-xl'])

<div id="{{ $id }}" class="app-modal hidden fixed inset-0 z-[60] items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
    <div class="w-full {{ $width }} max-h-[92vh] overflow-y-auto rounded-xl bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
            <div>
                <h2 class="font-bold" data-modal-title>{{ $title }}</h2>
                <p class="text-[12px] text-slate-400" data-modal-subtitle>{{ $subtitle }}</p>
            </div>
            <button type="button" onclick="closeModal('{{ $id }}')" class="text-slate-300 hover:text-white cursor-pointer" aria-label="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{ $slot }}
    </div>
</div>
