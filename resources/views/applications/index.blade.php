@extends('layouts.app')

@section('title', 'Postulaciones')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Postulaciones" description="Revisa la afinidad de cada estudiante con la oferta y decide si se asigna o se rechaza." />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card label="Postulaciones" :value="$counts['total']" />
            <x-stat-card label="Por revisar" :value="$counts['postuladas']" color="blue" />
            <x-stat-card label="Asignadas" :value="$counts['asignadas']" color="emerald" />
            <x-stat-card label="Rechazadas" :value="$counts['rechazadas']" color="red" />
        </div>

        <x-table-card title="Listado de postulaciones" :total="$counts['total']" search-id="applicationSearch" search-placeholder="Buscar por estudiante, oferta, empresa o estado...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </x-slot:icon>

            <table id="applicationsTable" class="w-full min-w-[1040px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Estudiante</th>
                        <th class="px-6 py-3.5">Oferta</th>
                        <th class="px-6 py-3.5">Afinidad</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5">Fecha</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($applications as $application)
                        @php
                            $studentUser = $application->student?->user;
                            $studentName = trim(($studentUser?->name ?? '').' '.($studentUser?->last_name ?? '')) ?: 'Estudiante sin cuenta';
                            $companyName = $application->offer?->company?->razon_social ?? $application->offer?->empresa;
                            $score = (float) $application->matching_score;
                            $scoreColor = $score >= 70 ? 'bg-emerald-500' : ($score >= 40 ? 'bg-amber-500' : 'bg-red-600');
                            $showData = [
                                'estudiante' => $studentName,
                                'correo' => $studentUser?->email,
                                'carrera' => $application->student?->career?->nombre ?? $application->student?->carrera,
                                'oferta' => $application->offer?->titulo,
                                'empresa' => $companyName,
                                'afinidad' => rtrim(rtrim(number_format($score, 2, ',', '.'), '0'), ',').'%',
                                'estado' => $application->status,
                                'fecha' => $application->created_at?->format('d-m-Y H:i'),
                                'motivacion' => $application->motivation,
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-[13.5px]">{{ $studentName }}</p>
                                <p class="text-[12px] text-slate-500">
                                    @if ($application->student?->career)
                                        <span class="font-mono font-bold">{{ $application->student->career->codigo }}</span> ·
                                    @endif
                                    {{ $studentUser?->email }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-800">{{ $application->offer?->titulo }}</p>
                                <p class="text-[12px] text-slate-500">{{ $companyName }}</p>
                            </td>
                            <td class="px-6 py-4" data-order="{{ $score }}">
                                <div class="flex items-center gap-2.5 w-36">
                                    <div class="h-2 flex-1 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full {{ $scoreColor }}" style="width: {{ min($score, 100) }}%"></div>
                                    </div>
                                    <span class="text-[12px] font-bold text-slate-700 w-10 text-right">{{ round($score) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-status-badge :status="$application->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" data-order="{{ $application->created_at?->timestamp }}">
                                {{ $application->created_at?->format('d-m-Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-action-button type="view" title="Ver postulación"
                                        onclick="openShowModal('showApplicationModal', {{ json_encode($showData) }})" />

                                    @if ($application->cv_path)
                                        <a href="{{ route('applications.cv', $application) }}" title="Descargar CV"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                    @endif

                                    @if ($application->status === 'Postulado')
                                        <form id="assign-application-{{ $application->id }}" action="{{ route('applications.assign', $application) }}" method="POST">
                                            @csrf
                                            <x-action-button type="approve" title="Asignar práctica"
                                                onclick="confirmSubmit('assign-application-{{ $application->id }}', { title: '¿Asignar práctica?', text: {{ json_encode($studentName.' quedará asignado a «'.$application->offer?->titulo.'» y se creará su práctica.') }}, confirmText: 'Sí, asignar', color: '#047857', icon: 'question' })" />
                                        </form>
                                        <form id="reject-application-{{ $application->id }}" action="{{ route('applications.reject', $application) }}" method="POST">
                                            @csrf
                                            <x-action-button type="reject" title="Rechazar postulación"
                                                onclick="confirmSubmit('reject-application-{{ $application->id }}', { title: '¿Rechazar postulación?', text: {{ json_encode('Se rechazará la postulación de '.$studentName.'.') }}, confirmText: 'Sí, rechazar' })" />
                                        </form>
                                    @endif

                                    @if ($canDelete)
                                        <form id="delete-application-{{ $application->id }}" action="{{ route('applications.destroy', $application) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-action-button type="delete" title="Eliminar postulación"
                                                onclick="confirmSubmit('delete-application-{{ $application->id }}', { title: '¿Eliminar postulación?', text: 'También se borrará el CV adjunto. Esta acción no se puede deshacer.', confirmText: 'Sí, eliminar' })" />
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-card>

        <p class="text-[12px] text-slate-500">
            La afinidad se calcula al postular: 40% por carrera, 25% por nivel de avance y 35% por habilidades en común con la oferta.
        </p>
    </div>

    {{-- Modal: detalle de la postulación --}}
    <x-form-modal id="showApplicationModal" title="Detalle de la postulación" width="max-w-2xl">
        <div class="px-6 py-5 space-y-4 text-[13px]">
            @php
                $detailFields = [
                    'estudiante' => 'Estudiante', 'correo' => 'Correo', 'carrera' => 'Carrera', 'oferta' => 'Oferta',
                    'empresa' => 'Empresa', 'afinidad' => 'Afinidad', 'estado' => 'Estado', 'fecha' => 'Fecha de postulación',
                ];
            @endphp
            <dl class="grid gap-x-6 gap-y-3 sm:grid-cols-2 rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                @foreach ($detailFields as $key => $label)
                    <div>
                        <dt class="text-[11.5px] text-slate-500">{{ $label }}</dt>
                        <dd class="font-semibold text-slate-800" data-show="{{ $key }}"></dd>
                    </div>
                @endforeach
            </dl>
            <div>
                <p class="text-[11.5px] text-slate-500 mb-1">Carta de motivación</p>
                <p class="whitespace-pre-line text-slate-700 leading-relaxed" data-show="motivacion"></p>
            </div>
        </div>
    </x-form-modal>
@endsection

@push('scripts')
    <script>
        initDataTable('#applicationsTable', '#applicationSearch', { order: [[2, 'desc']] });
    </script>
@endpush
