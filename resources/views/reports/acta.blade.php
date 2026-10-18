@extends('layouts.app')

@section('title', 'Acta de cierre')

@section('content')
    <article class="mx-auto max-w-4xl space-y-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8 print:border-0 print:shadow-none">
        <header class="flex flex-col gap-4 border-b-2 border-red-700 pb-5 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase text-red-700">Plataforma de prácticas profesionales</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Acta de cierre</h1>
                <p class="mt-1 text-sm text-slate-600">Registro {{ str_pad((string) $practice->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <button type="button" onclick="window.print()" class="no-print rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Imprimir / Guardar PDF</button>
        </header>

        <section class="grid gap-4 sm:grid-cols-2">
            <div><p class="text-xs font-semibold uppercase text-slate-500">Estudiante</p><p class="mt-1 font-semibold text-slate-900">{{ $practice->application?->student?->user?->name }} {{ $practice->application?->student?->user?->last_name }}</p></div>
            <div><p class="text-xs font-semibold uppercase text-slate-500">Carrera</p><p class="mt-1 font-semibold text-slate-900">{{ $practice->application?->student?->career?->nombre }}</p></div>
            <div><p class="text-xs font-semibold uppercase text-slate-500">Empresa</p><p class="mt-1 font-semibold text-slate-900">{{ $practice->application?->offer?->company?->razon_social }}</p></div>
            <div><p class="text-xs font-semibold uppercase text-slate-500">Oferta</p><p class="mt-1 font-semibold text-slate-900">{{ $practice->application?->offer?->titulo }}</p></div>
            <div><p class="text-xs font-semibold uppercase text-slate-500">Período</p><p class="mt-1 text-slate-800">{{ $practice->fecha_inicio?->format('d/m/Y') ?? '—' }} al {{ $practice->fecha_termino?->format('d/m/Y') ?? '—' }}</p></div>
            <div><p class="text-xs font-semibold uppercase text-slate-500">Modalidad</p><p class="mt-1 text-slate-800">{{ $practice->application?->offer?->modalidad ?? '—' }}</p></div>
        </section>

        <section>
            <h2 class="mb-2 border-b border-slate-200 pb-2 text-sm font-bold uppercase text-slate-800">Competencias evaluadas</h2>
            <ul class="grid gap-2 sm:grid-cols-2">
                @forelse ($competencies as $competency)
                    <li class="rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">{{ $competency }}</li>
                @empty
                    <li class="text-sm text-slate-500">Sin competencias registradas.</li>
                @endforelse
            </ul>
        </section>

        <section class="overflow-x-auto">
            <h2 class="mb-2 border-b border-slate-200 pb-2 text-sm font-bold uppercase text-slate-800">Evaluaciones</h2>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-2">Evaluador</th><th class="px-3 py-2">Responsable</th><th class="px-3 py-2 text-right">Nota</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($practice->evaluations->whereIn('tipo', ['supervisor', 'coordinador']) as $evaluation)
                        <tr><td class="px-3 py-2">{{ ucfirst($evaluation->tipo) }}</td><td class="px-3 py-2">{{ $evaluation->evaluator?->name }} {{ $evaluation->evaluator?->last_name }}</td><td class="px-3 py-2 text-right font-semibold">{{ number_format((float) $evaluation->calificacion, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="flex flex-col gap-2 border-t-2 border-slate-800 pt-4 sm:flex-row sm:items-end sm:justify-between">
            <div><p class="text-xs font-semibold uppercase text-slate-500">Calificación final ponderada</p><p class="mt-1 text-3xl font-bold text-red-700">{{ number_format((float) $practice->calificacion_final, 2) }}</p></div>
            <p class="text-sm text-slate-600">Finalizada {{ $practice->finalizada_at?->format('d/m/Y') ?? '—' }}</p>
        </section>
    </article>
@endsection

@push('scripts')
    <style>
        @media print {
            aside, .app-header, .no-print { display: none !important; }
            main { overflow: visible !important; padding: 0 !important; }
            body { display: block !important; height: auto !important; background: white !important; }
        }
    </style>
@endpush
<div>
    <!-- Always remember that you are absolutely unique. Just like everyone else. - Margaret Mead -->
</div>
