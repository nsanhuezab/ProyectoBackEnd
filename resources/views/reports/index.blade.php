@extends('layouts.app')

@section('title', 'Indicadores institucionales')

@section('content')
    <section class="space-y-6">
        <header class="flex flex-col gap-4 border-b border-slate-300 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase text-red-700">Vinculación y empleabilidad</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Indicadores de prácticas</h1>
            </div>
            <div class="no-print flex flex-wrap gap-2">
                @if (request()->is('coordinator/*'))
                    <a href="{{ route('coordinator.reports.excel') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="fa-solid fa-file-excel mr-2" aria-hidden="true"></i>Excel (.xlsx)
                    </a>
                    <a href="{{ route('coordinator.reports.export') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">CSV</a>
                @else
                    <a href="{{ route('admin.reports.excel') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="fa-solid fa-file-excel mr-2" aria-hidden="true"></i>Excel (.xlsx)
                    </a>
                    <a href="{{ route('admin.reports.export') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">CSV</a>
                @endif
                <button type="button" onclick="window.print()" class="rounded-md bg-red-700 px-3 py-2 text-sm font-semibold text-white hover:bg-red-800">
                    <i class="fa-solid fa-print mr-2" aria-hidden="true"></i>Imprimir / PDF
                </button>
            </div>
        </header>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Prácticas registradas</p><p class="mt-2 text-2xl font-bold text-slate-900">{{ $total }}</p>
            </article>
            <article class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Prácticas activas</p><p class="mt-2 text-2xl font-bold text-slate-900">{{ $active }}</p>
            </article>
            <article class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Tasa de aprobación</p><p class="mt-2 text-2xl font-bold text-emerald-700">{{ $approvalRate }}%</p>
            </article>
            <article class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Contratación post-práctica</p><p class="mt-2 text-2xl font-bold text-red-700">{{ $employmentRate }}%</p>
            </article>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            @foreach ([['Prácticas por carrera', $byCareer], ['Prácticas por empresa', $byCompany], ['Modalidad', $byModality], ['Rubro', $bySector]] as [$title, $rows])
                <section class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                    <h2 class="border-b border-slate-200 px-4 py-3 text-sm font-bold text-slate-800">{{ $title }}</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-2">Categoría</th><th class="px-4 py-2 text-right">Cantidad</th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($rows as $row)
                                    <tr><td class="px-4 py-2.5 text-slate-700">{{ $row['label'] }}</td><td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ $row['count'] }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-6 text-center text-slate-500">Sin datos disponibles</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </div>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <h2 class="border-b border-slate-200 px-4 py-3 text-sm font-bold text-slate-800">Empresas mejor evaluadas por estudiantes</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-2">Empresa</th><th class="px-4 py-2 text-right">Promedio</th><th class="px-4 py-2 text-right">Evaluaciones</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($companyRatings as $rating)
                            <tr><td class="px-4 py-2.5 font-medium text-slate-700">{{ $rating['label'] }}</td><td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ $rating['average'] }}</td><td class="px-4 py-2.5 text-right text-slate-600">{{ $rating['count'] }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">Aún no hay evaluaciones estudiantiles.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>
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
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
</div>
