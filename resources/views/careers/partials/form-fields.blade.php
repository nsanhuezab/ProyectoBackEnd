{{--
    Campos compartidos por los modales "Nueva carrera" y "Editar carrera".
    $prefix: 'create' o 'edit' (se usa para los id y para recuperar old() tras un error de validación).
--}}
@php
    $isThisForm = old('_modal') === $prefix;
    $inputClass = 'w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100 transition';
@endphp

<input type="hidden" name="_modal" value="{{ $prefix }}">

@if ($isThisForm && $errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[12.5px] text-red-800" role="alert">
        <p class="font-bold mb-1">Revisa los siguientes campos:</p>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label for="{{ $prefix }}-codigo" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
            Código <span class="text-red-600">*</span>
        </label>
        <input type="text" name="codigo" id="{{ $prefix }}-codigo" required maxlength="20"
            value="{{ $isThisForm ? old('codigo') : '' }}" placeholder="Ej: ICI"
            class="{{ $inputClass }} uppercase font-mono">
    </div>

    <div class="sm:col-span-2">
        <label for="{{ $prefix }}-nombre" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
            Nombre de la carrera <span class="text-red-600">*</span>
        </label>
        <input type="text" name="nombre" id="{{ $prefix }}-nombre" required maxlength="255"
            value="{{ $isThisForm ? old('nombre') : '' }}" placeholder="Ej: Ingeniería Civil Informática"
            class="{{ $inputClass }}">
    </div>
</div>

<div>
    <p class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
        Ponderación de la evaluación <span class="text-red-600">*</span>
    </p>
    <div class="grid gap-4 sm:grid-cols-2 rounded-lg border border-slate-200 bg-slate-50/60 p-3.5">
        <div>
            <label for="{{ $prefix }}-supervisor" class="block text-[12px] font-semibold text-slate-600 mb-1">Supervisor (%)</label>
            <input type="number" name="supervisor" id="{{ $prefix }}-supervisor" required min="0" max="100" step="0.01"
                value="{{ $isThisForm ? old('supervisor') : 50 }}" data-weight="supervisor" data-prefix="{{ $prefix }}"
                class="{{ $inputClass }} bg-white">
        </div>
        <div>
            <label for="{{ $prefix }}-coordinador" class="block text-[12px] font-semibold text-slate-600 mb-1">Coordinador (%)</label>
            <input type="number" name="coordinador" id="{{ $prefix }}-coordinador" required min="0" max="100" step="0.01"
                value="{{ $isThisForm ? old('coordinador') : 50 }}" data-weight="coordinador" data-prefix="{{ $prefix }}"
                class="{{ $inputClass }} bg-white">
        </div>
        <p class="sm:col-span-2 text-[11.5px] text-slate-500">
            Ambos porcentajes deben sumar 100. Al cambiar uno, el otro se ajusta solo.
        </p>
    </div>
</div>

<label for="{{ $prefix }}-activa" class="flex items-center gap-3 rounded-lg border border-slate-200 px-3.5 py-3 cursor-pointer hover:bg-slate-50">
    <input type="hidden" name="activa" value="0">
    <input type="checkbox" name="activa" id="{{ $prefix }}-activa" value="1"
        @checked($isThisForm ? old('activa') : true)
        class="h-4 w-4 rounded border-slate-300 text-red-700 focus:ring-red-600">
    <span>
        <span class="block text-[13px] font-bold text-slate-700">Carrera activa</span>
        <span class="block text-[11.5px] text-slate-500">Desmárcala si la carrera ya no se imparte; sus datos se conservan.</span>
    </span>
</label>
