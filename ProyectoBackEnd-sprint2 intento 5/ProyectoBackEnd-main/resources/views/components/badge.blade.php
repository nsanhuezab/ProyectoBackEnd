@props(['value', 'label' => null])

@php
    $color = match ($value) {
        'abierta', 'activa', 'activo', 'aceptada', 'en_curso', 'finalizada' => 'bg-green-100 text-green-800',
        'pendiente', 'borrador' => 'bg-amber-100 text-amber-800',
        'rechazada', 'cancelada', 'cerrada', 'inactiva', 'inactivo' => 'bg-red-100 text-red-800',
        'retirada' => 'bg-gray-100 text-gray-700',
        'administrador' => 'bg-ucsc-100 text-ucsc-800',
        'coordinador' => 'bg-indigo-100 text-indigo-800',
        'estudiante' => 'bg-sky-100 text-sky-800',
        'empresa' => 'bg-teal-100 text-teal-800',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $color }}">{{ $label ?? $value }}</span>
