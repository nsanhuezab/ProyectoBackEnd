{{-- Botones Cancelar / Guardar al final de un formulario en modal. --}}
@props(['modal', 'label' => 'Guardar'])

<div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
    <button type="button" onclick="closeModal('{{ $modal }}')"
        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
    <button type="submit"
        class="rounded-lg bg-[#C8102E] px-4 py-2 text-sm font-semibold text-white hover:bg-[#9e0b22] cursor-pointer">{{ $label }}</button>
</div>
