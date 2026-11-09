@extends('layouts.app')

@section('title', 'Postulantes')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Postulantes a mis ofertas" section="Área empresa"
            description="Perfil simplificado de cada estudiante (sin datos de contacto ni datos sensibles). La asignación final la decide el coordinador de carrera." />

        <x-table-card title="Postulantes" :total="$applications->count()" search-id="applicantSearch" search-placeholder="Buscar por oferta, carrera o habilidad...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </x-slot:icon>

            <table id="applicantsTable" class="w-full min-w-[960px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Estudiante</th>
                        <th class="px-6 py-3.5">Oferta</th>
                        <th class="px-6 py-3.5">Carrera y nivel</th>
                        <th class="px-6 py-3.5">Habilidades</th>
                        <th class="px-6 py-3.5">Afinidad</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5">Motivación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($applications as $application)
                        @php
                            $student = $application->student;
                            // Solo nombre e inicial del apellido: sin correo, RUT, teléfono ni notas.
                            $displayName = trim(($student?->user?->name ?? 'Estudiante').' '.mb_substr($student?->user?->last_name ?? '', 0, 1).'.');
                            $score = (float) $application->matching_score;
                        @endphp
                        <tr class="align-top hover:bg-gray-50/70">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $displayName }}</td>
                            <td class="px-6 py-4">{{ $application->offer?->titulo }}</td>
                            <td class="px-6 py-4">
                                <p class="text-slate-800">{{ $student?->career?->nombre ?? '—' }}</p>
                                <p class="text-[12px] text-slate-500">{{ $student?->nivel_avance }}{{ $student?->year_level ? ' · '.$student->year_level.'° año' : '' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-[220px]">
                                    @foreach ($student?->habilidades ?? [] as $skill)
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-[11px] font-semibold text-slate-600">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4" data-order="{{ $score }}"><span class="font-bold text-slate-800">{{ round($score) }}%</span></td>
                            <td class="px-6 py-4 text-center"><x-status-badge :status="$application->status" /></td>
                            <td class="px-6 py-4 text-[12.5px] text-slate-600 max-w-[280px]">{{ \Illuminate\Support\Str::limit($application->motivation, 180) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-card>
    </div>
@endsection

@push('scripts')
    <script>
        initDataTable('#applicantsTable', '#applicantSearch', { order: [[4, 'desc']], columnDefs: [] });
    </script>
@endpush
