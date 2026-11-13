@extends('layouts.app')

@section('title', 'Practicantes')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Practicantes en mi empresa" section="Área empresa"
            description="Al cierre de cada práctica, el supervisor completa la rúbrica de evaluación de desempeño." />

        <x-table-card title="Prácticas" :total="$practices->count()" search-id="companyPracticeSearch" search-placeholder="Buscar por estudiante, oferta o estado...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            </x-slot:icon>

            <table id="companyPracticesTable" class="w-full min-w-[860px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Practicante</th>
                        <th class="px-6 py-3.5">Oferta</th>
                        <th class="px-6 py-3.5">Período</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5 text-center">Rúbrica del supervisor</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($practices as $practice)
                        @php
                            $user = $practice->application?->student?->user;
                            $evaluation = $practice->evaluations->firstWhere('evaluator_role', \App\Models\PracticeEvaluation::SUPERVISOR);
                            $pending = ! $evaluation && in_array($practice->estado, ['Informe Entregado', 'Evaluado'], true);
                        @endphp
                        <tr class="hover:bg-gray-50/70">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ trim(($user?->name ?? '').' '.($user?->last_name ?? '')) }}</p>
                                <p class="text-[12px] text-slate-500">{{ $practice->application?->student?->career?->nombre }}</p>
                            </td>
                            <td class="px-6 py-4">{{ $practice->application?->offer?->titulo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $practice->fecha_inicio?->format('d-m-Y') ?? '—' }} → {{ $practice->fecha_termino?->format('d-m-Y') ?? '—' }}</td>
                            <td class="px-6 py-4 text-center"><x-status-badge :status="$practice->estado" /></td>
                            <td class="px-6 py-4 text-center">
                                @if ($evaluation)
                                    <span class="font-bold text-emerald-700">{{ number_format((float) $evaluation->grade, 1, ',', '') }}</span>
                                @elseif ($pending)
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pendiente</span>
                                @else
                                    <span class="text-slate-400 text-[12px]">Al cierre</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('practices.show', $practice) }}" class="rounded-lg {{ $pending ? 'bg-slate-900 text-white hover:bg-slate-800' : 'border border-slate-300 text-slate-700 hover:bg-slate-50' }} px-3 py-1.5 text-[12.5px] font-semibold">
                                    {{ $pending ? 'Evaluar' : 'Ver' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-card>
    </div>
@endsection

@push('scripts')
    <script>
        initDataTable('#companyPracticesTable', '#companyPracticeSearch', { order: [] });
    </script>
@endpush
