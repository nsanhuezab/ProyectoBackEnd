@extends('layouts.app')

@php
    $application = $practice->application;
    $student = $application?->student;
    $studentUser = $student?->user;
    $studentName = trim(($studentUser?->name ?? '').' '.($studentUser?->last_name ?? '')) ?: 'Estudiante';
    $offer = $application?->offer;
    $company = $offer?->company;
    $career = $student?->career;
    $stepIndex = array_search($practice->estado, $states, true);
    $nextState = $stepIndex === false ? null : ($states[$stepIndex + 1] ?? null);
    $supervisorEvaluation = $practice->evaluationBy(\App\Models\PracticeEvaluation::SUPERVISOR);
    $coordinatorEvaluation = $practice->evaluationBy(\App\Models\PracticeEvaluation::COORDINATOR);
    $approved = $practice->isApproved();
    $totalHours = $practice->logs->sum('hours');
    $grade = fn ($value) => $value === null ? '—' : number_format((float) $value, 1, ',', '');
@endphp

@section('title', 'Práctica de '.$studentName)

@section('content')
    <div class="space-y-6">
        <x-page-header :title="'Práctica de '.$studentName" :description="($offer?->titulo ?? 'Oferta').' · '.($company?->razon_social ?? $offer?->empresa)" section="Prácticas">
            <x-slot:actions>
                @if ($can['manage'])
                    <a href="{{ route('practices.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Volver a prácticas</a>
                @endif
                @if ($practice->final_grade !== null)
                    <a href="{{ route('practices.acta', $practice) }}" class="inline-flex items-center gap-2 rounded-lg bg-[#C8102E] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-[#9e0b22]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Acta de cierre (PDF)
                    </a>
                @endif
            </x-slot:actions>
        </x-page-header>

        {{-- Avance del flujo de estados --}}
        <div class="rounded-xl bg-white p-4 sm:p-5 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <ol class="flex flex-1 items-start sm:items-center w-full min-w-0 sm:min-w-[320px]">
                    @foreach ($states as $index => $state)
                        @php $done = $index <= $stepIndex; @endphp
                        <li class="flex items-start sm:items-center {{ $loop->last ? '' : 'flex-1' }}">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold {{ $done ? 'bg-red-700 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">{{ $index + 1 }}</span>
                                <span class="max-w-[60px] sm:max-w-none text-center leading-tight text-[10px] sm:text-[11px] font-semibold sm:whitespace-nowrap {{ $done ? 'text-slate-800' : 'text-slate-400' }}">{{ $state }}</span>
                            </div>
                            @if (! $loop->last)
                                <span class="h-0.5 flex-1 mx-1 sm:mx-2 mt-4 sm:mt-0 sm:mb-5 {{ $index < $stepIndex ? 'bg-red-700' : 'bg-slate-200' }}"></span>
                            @endif
                        </li>
                    @endforeach
                </ol>

                @if ($can['manage'] && $nextState)
                    <form id="transition-practice" action="{{ route('practices.transition', $practice) }}" method="POST">
                        @csrf
                        <input type="hidden" name="estado" value="{{ $nextState }}">
                        <button type="button"
                            onclick="confirmSubmit('transition-practice', { title: {{ json_encode('¿Pasar a «'.$nextState.'»?') }}, text: {{ json_encode($nextState === 'Evaluado' ? 'Se calculará la nota final con las dos rúbricas.' : 'Se avisará al estudiante.') }}, confirmText: 'Sí, avanzar', color: '#0369a1', icon: 'question' })"
                            class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 cursor-pointer">
                            Pasar a «{{ $nextState }}» →
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Datos de la práctica --}}
            <div class="lg:col-span-2 rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-[15px] font-bold text-slate-800">Datos de la práctica</h2>
                </div>
                <dl class="grid gap-x-6 gap-y-4 p-5 sm:grid-cols-2 text-[13px]">
                    <div><dt class="text-[11.5px] text-slate-500">Estudiante</dt><dd class="font-semibold text-slate-800">{{ $studentName }}</dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Carrera</dt><dd class="font-semibold text-slate-800">{{ $career?->nombre ?? '—' }}</dd></div>
                    @if ($can['seeStudentContact'])
                        <div><dt class="text-[11.5px] text-slate-500">Correo del estudiante</dt><dd class="font-semibold text-slate-800">{{ $studentUser?->email ?? '—' }}</dd></div>
                    @endif
                    <div><dt class="text-[11.5px] text-slate-500">Nivel / año</dt><dd class="font-semibold text-slate-800">{{ $student?->nivel_avance ?? '—' }}{{ $student?->year_level ? ' · '.$student->year_level.'° año' : '' }}</dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Empresa</dt><dd class="font-semibold text-slate-800">{{ $company?->razon_social ?? $offer?->empresa }}</dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Supervisor de práctica</dt><dd class="font-semibold text-slate-800">{{ $company?->contacto_nombre ?? '—' }}</dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Cargo / oferta</dt><dd class="font-semibold text-slate-800">{{ $offer?->titulo }}</dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Modalidad</dt><dd class="font-semibold text-slate-800">{{ $offer?->modalidad ?? '—' }}</dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Período</dt><dd class="font-semibold text-slate-800">{{ $practice->fecha_inicio?->format('d-m-Y') ?? '—' }} → {{ $practice->fecha_termino?->format('d-m-Y') ?? '—' }}</dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Horas registradas en bitácora</dt><dd class="font-semibold text-slate-800">{{ $totalHours }} h ({{ $practice->logs->count() }} registros)</dd></div>
                    <div>
                        <dt class="text-[11.5px] text-slate-500">Contratado por la empresa</dt>
                        <dd class="font-semibold text-slate-800">{{ $practice->hired === null ? 'Sin información' : ($practice->hired ? 'Sí' : 'No') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Nota final --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-5 flex flex-col">
                <h2 class="text-[15px] font-bold text-slate-800">Nota final</h2>
                <p class="text-[12px] text-slate-500">Supervisor {{ rtrim(rtrim(number_format((float) $career?->supervisor, 2, ',', ''), '0'), ',') }}% · Coordinador {{ rtrim(rtrim(number_format((float) $career?->coordinador, 2, ',', ''), '0'), ',') }}%</p>
                <div class="flex-1 flex flex-col items-center justify-center py-6">
                    <p class="text-5xl font-extrabold {{ $approved === null ? 'text-slate-300' : ($approved ? 'text-emerald-700' : 'text-red-700') }}">{{ $grade($practice->final_grade) }}</p>
                    @if ($approved !== null)
                        <span class="mt-2 inline-flex px-3 py-1 rounded-full text-[12px] font-bold {{ $approved ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                            {{ $approved ? 'Aprobada' : 'Reprobada' }}
                        </span>
                    @else
                        <p class="mt-2 text-[12px] text-slate-500 text-center">Se calcula cuando están las dos rúbricas.</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-2 text-center text-[12px]">
                    <div class="rounded-lg bg-slate-50 p-2"><p class="text-slate-500">Supervisor</p><p class="font-bold text-slate-800">{{ $grade($supervisorEvaluation?->grade) }}</p></div>
                    <div class="rounded-lg bg-slate-50 p-2"><p class="text-slate-500">Coordinador</p><p class="font-bold text-slate-800">{{ $grade($coordinatorEvaluation?->grade) }}</p></div>
                </div>
            </div>
        </div>

        {{-- Rúbricas --}}
        <div class="grid gap-6 lg:grid-cols-2">
            @php
                $rubrics = [
                    \App\Models\PracticeEvaluation::SUPERVISOR => ['Evaluación del supervisor', $supervisorEvaluation, $can['evaluateSupervisor']],
                    \App\Models\PracticeEvaluation::COORDINATOR => ['Evaluación del coordinador', $coordinatorEvaluation, $can['evaluateCoordinator']],
                ];
            @endphp
            @foreach ($rubrics as $role => [$title, $evaluation, $canEvaluate])
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                        <div>
                            <h2 class="text-[15px] font-bold text-slate-800">{{ $title }}</h2>
                            <p class="text-[12px] text-slate-500">
                                @if ($evaluation)
                                    {{ $evaluation->evaluator ? trim($evaluation->evaluator->name.' '.$evaluation->evaluator->last_name) : 'Registrada' }} · {{ $evaluation->updated_at?->format('d-m-Y') }}
                                @else
                                    Pendiente
                                @endif
                            </p>
                        </div>
                        @if ($canEvaluate)
                            <button type="button" onclick="openModal('{{ $role }}EvaluationModal')"
                                class="rounded-lg bg-slate-900 px-3 py-2 text-[12.5px] font-semibold text-white hover:bg-slate-800 cursor-pointer">
                                {{ $evaluation ? 'Editar rúbrica' : 'Completar rúbrica' }}
                            </button>
                        @endif
                    </div>

                    @if ($evaluation)
                        <table class="w-full text-[13px]">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($competencies as $key => $label)
                                    <tr>
                                        <td class="px-5 py-2.5 text-slate-600">{{ $label }}</td>
                                        <td class="px-5 py-2.5 text-right font-bold text-slate-800">{{ $grade($evaluation->scores[$key] ?? null) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-slate-50">
                                    <td class="px-5 py-2.5 font-bold text-slate-800">Promedio</td>
                                    <td class="px-5 py-2.5 text-right font-extrabold text-red-700">{{ $grade($evaluation->grade) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        @if ($evaluation->comments)
                            <p class="px-5 py-4 text-[12.5px] text-slate-600 whitespace-pre-line border-t border-gray-100">{{ $evaluation->comments }}</p>
                        @endif
                    @else
                        <p class="px-5 py-8 text-center text-[13px] text-slate-500">
                            @if (! in_array($practice->estado, ['Informe Entregado', 'Evaluado', 'Finalizada'], true))
                                Se completa al cierre, cuando la práctica esté en «Informe Entregado».
                            @else
                                Aún no se completa esta rúbrica.
                            @endif
                        </p>
                    @endif
                </div>

                @if ($canEvaluate)
                    @php $modalId = $role.'EvaluationModal'; $oldScores = old('_modal') === $modalId ? old('scores', []) : []; @endphp
                    <x-form-modal :id="$modalId" :title="$title" subtitle="Califica cada competencia de 1,0 a 7,0.">
                        <form action="{{ route('practices.evaluations.store', $practice) }}" method="POST" class="space-y-5 p-6">
                            @csrf
                            <input type="hidden" name="_modal" value="{{ $modalId }}">
                            <input type="hidden" name="evaluator_role" value="{{ $role }}">
                            <x-form-errors :modal="$modalId" />

                            <div class="divide-y divide-slate-100 rounded-lg border border-slate-200">
                                @foreach ($competencies as $key => $label)
                                    <label class="flex items-center justify-between gap-4 px-4 py-2.5">
                                        <span class="text-[13px] text-slate-700">{{ $label }}</span>
                                        <input type="number" name="scores[{{ $key }}]" required min="1" max="7" step="0.1"
                                            value="{{ $oldScores[$key] ?? ($evaluation->scores[$key] ?? '') }}"
                                            class="form-input w-24 text-center font-bold">
                                    </label>
                                @endforeach
                            </div>
                            <x-field label="Comentarios" for="{{ $role }}-comments">
                                <textarea id="{{ $role }}-comments" name="comments" rows="3" maxlength="2000" class="form-input">{{ old('_modal') === $modalId ? old('comments') : $evaluation?->comments }}</textarea>
                            </x-field>

                            <x-form-actions :modal="$modalId" label="Guardar evaluación" />
                        </form>
                    </x-form-modal>
                @endif
            @endforeach
        </div>

        {{-- Bitácora quincenal --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800">Bitácora quincenal</h2>
                    <p class="text-[12px] text-slate-500">La registra el estudiante desde «Mi práctica». Total: {{ $totalHours }} horas.</p>
                </div>
            </div>
            @forelse ($practice->logs as $log)
                <div class="px-5 py-4 border-b border-gray-100 last:border-0">
                    <div class="flex items-center justify-between gap-4">
                        <p class="font-bold text-slate-800 text-[13px]">{{ $log->period_start->format('d-m-Y') }} → {{ $log->period_end->format('d-m-Y') }}</p>
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11.5px] font-bold text-slate-600">{{ $log->hours }} h</span>
                    </div>
                    <p class="mt-1.5 text-[13px] text-slate-700 whitespace-pre-line">{{ $log->activities }}</p>
                    @if ($log->learnings)
                        <p class="mt-1.5 text-[12.5px] text-slate-600"><span class="font-semibold">Aprendizajes:</span> {{ $log->learnings }}</p>
                    @endif
                    @if ($log->difficulties)
                        <p class="mt-1 text-[12.5px] text-slate-600"><span class="font-semibold">Dificultades:</span> {{ $log->difficulties }}</p>
                    @endif
                </div>
            @empty
                <p class="px-5 py-8 text-center text-[13px] text-slate-500">Todavía no hay registros en la bitácora.</p>
            @endforelse
        </div>

        {{-- Evaluación de la empresa por el estudiante --}}
        @if ($practice->review)
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 px-5 py-4">
                <h2 class="text-[15px] font-bold text-slate-800">Evaluación de la empresa hecha por el estudiante</h2>
                <p class="mt-1 text-xl text-amber-500" aria-label="{{ $practice->review->rating }} de 5">
                    {{ str_repeat('★', $practice->review->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $practice->review->rating) }}</span>
                </p>
                @if ($practice->review->comment)
                    <p class="mt-1 text-[13px] text-slate-600 whitespace-pre-line">{{ $practice->review->comment }}</p>
                @endif
            </div>
        @endif
    </div>
@endsection
