{{-- Etiqueta y campo de formulario. --}}
@props(['label', 'for' => null, 'required' => false, 'hint' => null])

<div {{ $attributes }}>
    <label @if ($for) for="{{ $for }}" @endif class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
        {{ $label }} @if ($required)<span class="text-red-600">*</span>@endif
    </label>
    {{ $slot }}
    @if ($hint)
        <p class="mt-1 text-[11.5px] text-slate-500">{{ $hint }}</p>
    @endif
</div>
