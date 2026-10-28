@php
    $name = $field['name'];
    $type = $field['type'] ?? 'text';
    $isPassword = $type === 'password';

    $value = old($name, $model?->{$name} ?? ($defaults[$name] ?? null));
    if ($value instanceof \DateTimeInterface) {
        $value = $value->format('Y-m-d');
    }
    if (is_bool($value)) {
        $value = (int) $value;
    }
    if ($isPassword) {
        $value = null;
    }

    $hasError = $errors->has($name);
    $inputClass = 'field-input '.($hasError ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : '');
    $required = in_array('required', (array) (($field['rules'] ?? []) instanceof \Closure ? $field['rules']($model) : ($field['rules'] ?? [])), true);
    $attrs = collect($field['attrs'] ?? [])->map(fn ($v, $k) => $k.'="'.e($v).'"')->implode(' ');
@endphp

<div class="{{ ! empty($field['full']) ? 'sm:col-span-2' : '' }}">
    <label for="{{ $name }}" class="field-label">
        {{ $field['label'] }}@if($required)<span class="text-red-500"> *</span>@endif
    </label>

    @if($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}" class="{{ $inputClass }}" @if($required) required @endif>
            @if(array_key_exists('placeholder', $field) || ! $required)
                <option value="">{{ $field['placeholder'] ?? '— Selecciona —' }}</option>
            @endif
            @foreach($field['options'] as $optKey => $optLabel)
                <option value="{{ $optKey }}" @selected((string) $value === (string) $optKey)>{{ $optLabel }}</option>
            @endforeach
        </select>

    @elseif($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="4" class="{{ $inputClass }}" @if($required) required @endif>{{ $value }}</textarea>

    @else
        <input id="{{ $name }}" name="{{ $name }}"
               type="{{ $type === 'rut' ? 'text' : $type }}"
               value="{{ $value }}"
               @if($type === 'rut') inputmode="text" autocomplete="off" maxlength="12" @endif
               @if($isPassword) autocomplete="new-password" @endif
               @if(! empty($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
               @if($required) required @endif
               {!! $attrs !!}
               class="{{ $inputClass }}">
    @endif

    @if(! empty($field['hint']))
        <p class="mt-1 text-xs text-gray-500">{{ $field['hint'] }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
