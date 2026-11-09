{{-- Etiqueta de estado con color según el valor (ofertas, postulaciones y prácticas). --}}
@props(['status'])

@php
    $styles = [
        'Pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Aprobada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Rechazada' => 'bg-red-50 text-red-700 border-red-200',
        'Postulado' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Rechazado' => 'bg-red-50 text-red-700 border-red-200',
        'Asignado' => 'bg-violet-50 text-violet-700 border-violet-200',
        'En Curso' => 'bg-sky-50 text-sky-700 border-sky-200',
        'Informe Entregado' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Evaluado' => 'bg-rose-50 text-rose-700 border-rose-200',
        'Finalizada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];
@endphp

<span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-[11.5px] font-bold border {{ $styles[$status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
    {{ $status }}
</span>
