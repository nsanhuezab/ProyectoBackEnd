{{-- Tarjeta de resumen con un número. color: slate | emerald | amber | red | blue --}}
@props(['label', 'value', 'color' => 'slate'])

@php
    $colors = [
        'slate' => 'text-slate-900',
        'emerald' => 'text-emerald-700',
        'amber' => 'text-amber-600',
        'red' => 'text-red-700',
        'blue' => 'text-blue-700',
        'muted' => 'text-slate-500',
    ];
@endphp

<div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</p>
    <p class="mt-1 text-2xl font-extrabold {{ $colors[$color] ?? $colors['slate'] }}">{{ $value }}</p>
</div>
