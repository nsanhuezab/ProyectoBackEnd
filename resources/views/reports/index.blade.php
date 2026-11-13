@extends('layouts.app')

@section('title', 'Indicadores')

@php
    $percent = fn ($value) => $value === null ? '—' : number_format($value, 1, ',', '').'%';
@endphp

@section('content')
    <div class="space-y-6">
        <x-page-header title="Panel de indicadores"
            :description="'Estado de las prácticas y empleabilidad'.($scope ? ' · Carrera: '.$scope : ' · Todas las carreras').'.'">
            <x-slot:actions>
                <a href="{{ route('reports.pdf') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#C8102E] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-[#9e0b22]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Exportar PDF
                </a>
                <a href="{{ route('reports.excel') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Exportar Excel
                </a>
            </x-slot:actions>
        </x-page-header>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card label="Estudiantes en práctica" :value="$totals['activas']" color="blue" />
            <x-stat-card label="Tasa de aprobación" :value="$percent($totals['aprobacion'])" color="emerald" />
            <x-stat-card label="Empleabilidad post-práctica" :value="$percent($totals['empleabilidad'])" color="red" />
            <x-stat-card label="Nota final promedio" :value="$totals['promedio'] === null ? '—' : number_format($totals['promedio'], 1, ',', '')" />
        </div>
        <p class="-mt-3 text-[12px] text-slate-500">
            {{ $totals['practicas'] }} prácticas registradas · {{ $totals['evaluadas'] }} con nota final (aprueba con {{ number_format($passingGrade, 1, ',', '') }}) ·
            {{ $totals['finalizadas'] }} finalizadas, {{ $totals['contratados'] }} estudiante(s) contratados por su empresa.
        </p>

        @php
            $groups = [
                ['Estudiantes en práctica por carrera', $byCareer],
                ['Estudiantes en práctica por empresa', $byCompany],
                ['Distribución por modalidad', $byModality],
                ['Distribución por rubro', $byIndustry],
            ];
        @endphp
        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ($groups as [$groupTitle, $rows])
                @php $max = max(1, $rows->max('total') ?? 1); @endphp
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="text-[15px] font-bold text-slate-800">{{ $groupTitle }}</h2>
                        <p class="text-[12px] text-slate-500"><span class="inline-block w-2.5 h-2.5 rounded-sm bg-red-700 align-middle"></span> en práctica ahora · <span class="inline-block w-2.5 h-2.5 rounded-sm bg-slate-300 align-middle"></span> total histórico</p>
                    </div>
                    <div class="space-y-3 p-5">
                        @forelse ($rows as $row)
                            <div>
                                <div class="flex justify-between text-[12.5px]">
                                    <span class="font-semibold text-slate-700">{{ $row['nombre'] }}</span>
                                    <span class="text-slate-500"><b class="text-slate-800">{{ $row['activas'] }}</b> / {{ $row['total'] }}</span>
                                </div>
                                {{-- Gris: total. Rojo: activas. --}}
                                <div class="mt-1 h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full bg-slate-300 relative" style="width: {{ round($row['total'] / $max * 100) }}%">
                                        <div class="absolute inset-y-0 left-0 bg-red-700" style="width: {{ $row['total'] ? round($row['activas'] / $row['total'] * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-[13px] text-slate-500 py-4">Sin datos todavía.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <x-table-card title="Ranking de empresas mejor evaluadas por los estudiantes" :total="$ranking->count()" search-id="rankingSearch" search-placeholder="Buscar empresa...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            </x-slot:icon>
            <table id="rankingTable" class="w-full text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">#</th>
                        <th class="px-6 py-3.5">Empresa</th>
                        <th class="px-6 py-3.5">Rubro</th>
                        <th class="px-6 py-3.5">Calificación</th>
                        <th class="px-6 py-3.5 text-center">Evaluaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($ranking as $position => $row)
                        <tr>
                            <td class="px-6 py-4 font-extrabold text-slate-400">{{ $position + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $row['empresa'] }}</td>
                            <td class="px-6 py-4">{{ $row['rubro'] }}</td>
                            <td class="px-6 py-4" data-order="{{ $row['promedio'] }}">
                                <span class="text-amber-500">{{ str_repeat('★', (int) round($row['promedio'])) }}</span><span class="text-slate-300">{{ str_repeat('★', 5 - (int) round($row['promedio'])) }}</span>
                                <span class="ml-1 font-bold text-slate-700">{{ number_format($row['promedio'], 1, ',', '') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $row['evaluaciones'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-card>
    </div>
@endsection

@push('scripts')
    <script>
        initDataTable('#rankingTable', '#rankingSearch', {
            order: [[3, 'desc']],
            columnDefs: [],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                emptyTable: 'Todavía no hay evaluaciones de empresas hechas por estudiantes.',
            },
        });
    </script>
@endpush
