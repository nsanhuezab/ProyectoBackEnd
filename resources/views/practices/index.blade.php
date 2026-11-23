@extends('layouts.app')

@section('title', 'Prácticas')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Prácticas" description="Sigue cada práctica desde que se asigna hasta que finaliza.">
            <x-slot:actions>
                <button type="button" onclick="openCreateModal('createPracticeModal')"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nueva práctica
                </button>
            </x-slot:actions>
        </x-page-header>

        <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-5">
            <x-stat-card label="Asignadas" :value="$counts['Asignado']" color="slate" />
            <x-stat-card label="En curso" :value="$counts['En Curso']" color="blue" />
            <x-stat-card label="Informe entregado" :value="$counts['Informe Entregado']" color="amber" />
            <x-stat-card label="Evaluadas" :value="$counts['Evaluado']" color="red" />
            <x-stat-card label="Finalizadas" :value="$counts['Finalizada']" color="emerald" />
        </div>

        <x-table-card title="Listado de prácticas" :total="$counts['total']" search-id="practiceSearch" search-placeholder="Buscar por estudiante, oferta, empresa o estado...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </x-slot:icon>

            <table id="practicesTable" class="w-full lg:min-w-[1040px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Estudiante</th>
                        <th class="px-6 py-3.5">Oferta</th>
                        <th class="px-6 py-3.5">Período</th>
                        <th class="px-6 py-3.5">Avance</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($practices as $practice)
                        @php
                            $application = $practice->application;
                            $studentUser = $application?->student?->user;
                            $studentName = trim(($studentUser?->name ?? '').' '.($studentUser?->last_name ?? '')) ?: 'Estudiante';
                            $stepIndex = array_search($practice->estado, $states, true);
                            $nextState = $stepIndex === false ? null : ($states[$stepIndex + 1] ?? null);
                            $rowData = [
                                'fecha_inicio' => $practice->fecha_inicio?->toDateString(),
                                'fecha_termino' => $practice->fecha_termino?->toDateString(),
                                'hired' => $practice->hired === null ? '' : ($practice->hired ? '1' : '0'),
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-[13.5px]">{{ $studentName }}</p>
                                <p class="text-[12px] text-slate-500">
                                    @if ($application?->student?->career)
                                        <span class="font-mono font-bold">{{ $application->student->career->codigo }}</span> ·
                                    @endif
                                    {{ $studentUser?->email }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-800">{{ $application?->offer?->titulo }}</p>
                                <p class="text-[12px] text-slate-500">{{ $application?->offer?->company?->razon_social ?? $application?->offer?->empresa }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" data-order="{{ $practice->fecha_inicio?->timestamp ?? 0 }}">
                                @if ($practice->fecha_inicio || $practice->fecha_termino)
                                    <span class="text-slate-700">{{ $practice->fecha_inicio?->format('d-m-Y') ?? '—' }}</span>
                                    <span class="text-slate-400 mx-1">→</span>
                                    <span class="text-slate-700">{{ $practice->fecha_termino?->format('d-m-Y') ?? '—' }}</span>
                                @else
                                    <span class="text-[12px] text-amber-600 font-semibold">Sin fechas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4" data-order="{{ $stepIndex }}" data-search="{{ $practice->estado }}">
                                {{-- Avance por estado --}}
                                <div class="w-56">
                                    <div class="flex items-center">
                                        @foreach ($states as $index => $state)
                                            <span title="{{ $state }}" class="w-3 h-3 rounded-full shrink-0 {{ $index <= $stepIndex ? ($practice->estado === 'Finalizada' ? 'bg-emerald-500' : 'bg-red-700') : 'bg-slate-200' }}"></span>
                                            @if (! $loop->last)
                                                <span class="h-0.5 flex-1 {{ $index < $stepIndex ? ($practice->estado === 'Finalizada' ? 'bg-emerald-500' : 'bg-red-700') : 'bg-slate-200' }}"></span>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="mt-1.5 flex items-center gap-2">
                                        <x-status-badge :status="$practice->estado" />
                                        @if ($practice->final_grade !== null)
                                            <span class="text-[12px] font-extrabold {{ $practice->isApproved() ? 'text-emerald-700' : 'text-red-700' }}">Nota {{ number_format((float) $practice->final_grade, 1, ',', '') }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-500">{{ $practice->logs_count }} bitácora(s) · {{ $practice->evaluations_count }}/2 rúbricas</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('practices.show', $practice) }}" title="Ver detalle, bitácora y rúbricas"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    @if ($nextState)
                                        <form id="transition-practice-{{ $practice->id }}" action="{{ route('practices.transition', $practice) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="estado" value="{{ $nextState }}">
                                            <x-action-button type="next" :title="'Pasar a '.$nextState"
                                                onclick="confirmSubmit('transition-practice-{{ $practice->id }}', { title: {{ json_encode('¿Pasar a «'.$nextState.'»?') }}, text: {{ json_encode('Práctica de '.$studentName.'.') }}, confirmText: 'Sí, avanzar', color: '#0369a1', icon: 'question' })" />
                                        </form>
                                    @endif

                                    <x-action-button type="edit" title="Editar fechas y contratación"
                                        onclick="openEditModal('editPracticeModal', {{ json_encode(route('practices.update', $practice)) }}, {{ json_encode($rowData) }}, {{ json_encode($studentName.' · '.$application?->offer?->titulo) }})" />

                                    @if ($canDelete)
                                        <form id="delete-practice-{{ $practice->id }}" action="{{ route('practices.destroy', $practice) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-action-button type="delete" title="Eliminar práctica"
                                                onclick="confirmSubmit('delete-practice-{{ $practice->id }}', { title: '¿Eliminar práctica?', text: 'La postulación volverá a quedar pendiente.', confirmText: 'Sí, eliminar' })" />
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-card>
    </div>

    {{-- Modal: nueva práctica --}}
    <x-form-modal id="createPracticeModal" title="Nueva práctica" subtitle="Elige una postulación pendiente; quedará asignada.">
        <form action="{{ route('practices.store') }}" method="POST" class="space-y-5 p-6">
            @csrf
            <input type="hidden" name="_modal" value="createPracticeModal">
            <input type="hidden" name="_action" value="">
            <x-form-errors modal="createPracticeModal" />

            @php $old = fn (string $key, $default = null) => old('_modal') === 'createPracticeModal' ? old($key, $default) : $default; @endphp

            @if ($pendingApplications->isEmpty())
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-[12.5px] text-amber-800">
                    No hay postulaciones pendientes para asignar.
                </div>
            @endif

            <x-field label="Postulación" for="create-application_id" required>
                <select id="create-application_id" name="application_id" required class="form-input">
                    <option value="">Selecciona una postulación</option>
                    @foreach ($pendingApplications as $pending)
                        <option value="{{ $pending->id }}" @selected((string) $old('application_id') === (string) $pending->id)>
                            {{ trim(($pending->student?->user?->name ?? '').' '.($pending->student?->user?->last_name ?? '')) }} · {{ $pending->offer?->titulo }} ({{ round((float) $pending->matching_score) }}% afinidad)
                        </option>
                    @endforeach
                </select>
            </x-field>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-field label="Fecha de inicio" for="create-fecha_inicio">
                    <input type="date" id="create-fecha_inicio" name="fecha_inicio" value="{{ $old('fecha_inicio') }}" class="form-input">
                </x-field>
                <x-field label="Fecha de término" for="create-fecha_termino">
                    <input type="date" id="create-fecha_termino" name="fecha_termino" value="{{ $old('fecha_termino') }}" class="form-input">
                </x-field>
            </div>

            <x-form-actions modal="createPracticeModal" label="Crear práctica" />
        </form>
    </x-form-modal>

    {{-- Modal: editar fechas --}}
    <x-form-modal id="editPracticeModal" title="Editar práctica" subtitle="">
        <form action="{{ old('_action', '#') }}" method="POST" class="space-y-5 p-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="editPracticeModal">
            <input type="hidden" name="_action" value="{{ old('_action') }}">
            <x-form-errors modal="editPracticeModal" />

            @php $old = fn (string $key, $default = null) => old('_modal') === 'editPracticeModal' ? old($key, $default) : $default; @endphp
            <div class="grid gap-4 sm:grid-cols-2">
                <x-field label="Fecha de inicio" for="edit-fecha_inicio">
                    <input type="date" id="edit-fecha_inicio" name="fecha_inicio" value="{{ $old('fecha_inicio') }}" class="form-input">
                </x-field>
                <x-field label="Fecha de término" for="edit-fecha_termino">
                    <input type="date" id="edit-fecha_termino" name="fecha_termino" value="{{ $old('fecha_termino') }}" class="form-input">
                </x-field>
            </div>
            <x-field label="¿La empresa contrató al estudiante después de la práctica?" for="edit-hired" hint="Se usa para la tasa de empleabilidad del panel de indicadores.">
                <select id="edit-hired" name="hired" class="form-input">
                    <option value="">Sin información</option>
                    <option value="1" @selected($old('hired') === '1')>Sí, fue contratado</option>
                    <option value="0" @selected($old('hired') === '0')>No</option>
                </select>
            </x-field>
            <p class="text-[12px] text-slate-500">El estado se cambia con el botón de avanzar en la tabla.</p>

            <x-form-actions modal="editPracticeModal" label="Guardar cambios" />
        </form>
    </x-form-modal>
@endsection

@push('scripts')
    <script>
        initDataTable('#practicesTable', '#practiceSearch', { order: [] });
    </script>
@endpush
