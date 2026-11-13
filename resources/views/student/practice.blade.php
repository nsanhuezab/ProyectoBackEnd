@extends('layouts.app')

@section('title', 'Mi práctica')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Mi práctica" section="Área estudiante"
            description="Sigue el avance de tu práctica, registra tu bitácora quincenal y evalúa a la empresa al terminar." />

        @forelse ($practices as $practice)
            @php
                $offer = $practice->application?->offer;
                $company = $offer?->company;
                $stepIndex = array_search($practice->estado, $states, true);
                $canLog = in_array($practice->estado, $logStates, true);
                $canReview = in_array($practice->estado, $reviewStates, true);
                $modalId = 'logModal'.$practice->id;
                $reviewModalId = 'reviewModal'.$practice->id;
                $lastLog = $practice->logs->last();
                $suggestedStart = $lastLog ? $lastLog->period_end->copy()->addDay() : ($practice->fecha_inicio ?? today());
                $suggestedEnd = $suggestedStart->copy()->addDays($periodDays - 1)->min(today());
            @endphp

            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="flex flex-col gap-3 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-red-700">{{ $company?->razon_social ?? $offer?->empresa }}</p>
                        <h2 class="text-lg font-extrabold text-slate-900">{{ $offer?->titulo }}</h2>
                        <p class="text-[12.5px] text-slate-500">
                            {{ $practice->fecha_inicio?->format('d-m-Y') ?? 'Sin fecha de inicio' }} → {{ $practice->fecha_termino?->format('d-m-Y') ?? 'sin fecha de término' }}
                            · Supervisor: {{ $company?->contacto_nombre ?? '—' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-status-badge :status="$practice->estado" />
                        @if ($practice->final_grade !== null)
                            <span class="text-2xl font-extrabold {{ $practice->isApproved() ? 'text-emerald-700' : 'text-red-700' }}">{{ number_format((float) $practice->final_grade, 1, ',', '') }}</span>
                            <a href="{{ route('practices.acta', $practice) }}" class="rounded-lg bg-[#C8102E] px-3 py-2 text-[12.5px] font-semibold text-white hover:bg-[#9e0b22]">Descargar acta</a>
                        @endif
                    </div>
                </div>

                {{-- Avance --}}
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center">
                        @foreach ($states as $index => $state)
                            <div class="flex flex-col items-center gap-1 shrink-0">
                                <span class="w-3.5 h-3.5 rounded-full {{ $index <= $stepIndex ? 'bg-red-700' : 'bg-slate-200' }}"></span>
                                <span class="text-[10.5px] font-semibold {{ $index <= $stepIndex ? 'text-slate-700' : 'text-slate-400' }}">{{ $state }}</span>
                            </div>
                            @if (! $loop->last)
                                <span class="h-0.5 flex-1 mb-4 {{ $index < $stepIndex ? 'bg-red-700' : 'bg-slate-200' }}"></span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-0 lg:grid-cols-3">
                    {{-- Bitácora --}}
                    <div class="lg:col-span-2 p-5 lg:border-r border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-slate-800">Bitácora quincenal</h3>
                                <p class="text-[12px] text-slate-500">{{ $practice->logs->count() }} registros · {{ $practice->logs->sum('hours') }} horas</p>
                            </div>
                            @if ($canLog)
                                <button type="button" onclick="openModal('{{ $modalId }}')" class="rounded-lg bg-slate-900 px-3 py-2 text-[12.5px] font-semibold text-white hover:bg-slate-800 cursor-pointer">+ Registrar avance</button>
                            @endif
                        </div>

                        @if (! $canLog && $practice->logs->isEmpty())
                            <p class="text-[12.5px] text-slate-500">La bitácora se habilita cuando tu práctica esté «En Curso».</p>
                        @endif

                        <div class="space-y-3">
                            @foreach ($practice->logs->sortByDesc('period_start') as $log)
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-slate-800 text-[13px]">{{ $log->period_start->format('d-m-Y') }} → {{ $log->period_end->format('d-m-Y') }} · {{ $log->hours }} h</p>
                                        @if ($canLog)
                                            <form id="delete-log-{{ $log->id }}" action="{{ route('student.practice.logs.destroy', $log) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-action-button type="delete" title="Eliminar registro"
                                                    onclick="confirmSubmit('delete-log-{{ $log->id }}', { title: '¿Eliminar este registro?', text: 'Se borrará de tu bitácora.', confirmText: 'Sí, eliminar' })" />
                                            </form>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-[13px] text-slate-700 whitespace-pre-line">{{ $log->activities }}</p>
                                    @if ($log->learnings)<p class="mt-1 text-[12px] text-slate-600"><b>Aprendizajes:</b> {{ $log->learnings }}</p>@endif
                                    @if ($log->difficulties)<p class="mt-1 text-[12px] text-slate-600"><b>Dificultades:</b> {{ $log->difficulties }}</p>@endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Evaluación de la empresa --}}
                    <div class="p-5 border-t lg:border-t-0 border-gray-100">
                        <h3 class="font-bold text-slate-800">Evalúa a la empresa</h3>
                        @if ($practice->review)
                            <p class="mt-2 text-2xl text-amber-500">{{ str_repeat('★', $practice->review->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $practice->review->rating) }}</span></p>
                            @if ($practice->review->comment)
                                <p class="mt-1 text-[12.5px] text-slate-600 whitespace-pre-line">{{ $practice->review->comment }}</p>
                            @endif
                        @endif
                        @if ($canReview)
                            <button type="button" onclick="openModal('{{ $reviewModalId }}')" class="mt-3 rounded-lg border border-slate-300 px-3 py-2 text-[12.5px] font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                                {{ $practice->review ? 'Cambiar mi evaluación' : 'Evaluar empresa' }}
                            </button>
                        @elseif (! $practice->review)
                            <p class="mt-2 text-[12.5px] text-slate-500">Podrás evaluar a la empresa cuando tu práctica esté evaluada. Tu opinión ayuda a otros estudiantes.</p>
                        @endif
                    </div>
                </div>
            </div>

            @if ($canLog)
                <x-form-modal :id="$modalId" title="Registrar avance quincenal" :subtitle="'Bitácora de «'.$offer?->titulo.'»'">
                    <form action="{{ route('student.practice.logs.store', $practice) }}" method="POST" class="space-y-4 p-6">
                        @csrf
                        <input type="hidden" name="_modal" value="{{ $modalId }}">
                        <x-form-errors :modal="$modalId" />
                        @php $old = fn (string $key, $default = null) => old('_modal') === $modalId ? old($key, $default) : $default; @endphp
                        <div class="grid gap-4 sm:grid-cols-3">
                            <x-field label="Desde" required>
                                <input type="date" name="period_start" required value="{{ $old('period_start', $suggestedStart->toDateString()) }}" class="form-input">
                            </x-field>
                            <x-field label="Hasta" required>
                                <input type="date" name="period_end" required value="{{ $old('period_end', $suggestedEnd->toDateString()) }}" class="form-input">
                            </x-field>
                            <x-field label="Horas trabajadas" required>
                                <input type="number" name="hours" required min="1" max="120" value="{{ $old('hours', 40) }}" class="form-input">
                            </x-field>
                        </div>
                        <x-field label="Actividades realizadas" required hint="Mínimo 20 caracteres.">
                            <textarea name="activities" rows="4" required minlength="20" maxlength="3000" class="form-input">{{ $old('activities') }}</textarea>
                        </x-field>
                        <x-field label="Aprendizajes">
                            <textarea name="learnings" rows="2" maxlength="3000" class="form-input">{{ $old('learnings') }}</textarea>
                        </x-field>
                        <x-field label="Dificultades">
                            <textarea name="difficulties" rows="2" maxlength="3000" class="form-input">{{ $old('difficulties') }}</textarea>
                        </x-field>
                        <x-form-actions :modal="$modalId" label="Guardar avance" />
                    </form>
                </x-form-modal>
            @endif

            @if ($canReview)
                <x-form-modal :id="$reviewModalId" title="Evaluar a la empresa" :subtitle="$company?->razon_social">
                    <form action="{{ route('student.practice.review.store', $practice) }}" method="POST" class="space-y-4 p-6">
                        @csrf
                        <input type="hidden" name="_modal" value="{{ $reviewModalId }}">
                        <x-form-errors :modal="$reviewModalId" />
                        @php $currentRating = (int) (old('_modal') === $reviewModalId ? old('rating') : $practice->review?->rating); @endphp
                        <x-field label="¿Cómo fue tu experiencia en la empresa?" required>
                            <div class="flex flex-row-reverse justify-end gap-1 text-3xl star-rating">
                                @for ($star = 5; $star >= 1; $star--)
                                    <input type="radio" id="rating-{{ $practice->id }}-{{ $star }}" name="rating" value="{{ $star }}" class="peer sr-only" @checked($currentRating === $star) required>
                                    <label for="rating-{{ $practice->id }}-{{ $star }}" class="cursor-pointer text-slate-300" title="{{ $star }} de 5">★</label>
                                @endfor
                            </div>
                        </x-field>
                        <x-field label="Comentario (opcional)">
                            <textarea name="comment" rows="3" maxlength="2000" class="form-input">{{ old('_modal') === $reviewModalId ? old('comment') : $practice->review?->comment }}</textarea>
                        </x-field>
                        <x-form-actions :modal="$reviewModalId" label="Enviar evaluación" />
                    </form>
                </x-form-modal>
            @endif
        @empty
            <div class="rounded-xl bg-white p-10 text-center shadow-sm ring-1 ring-gray-200">
                <p class="text-lg font-bold text-slate-800">Aún no tienes una práctica asignada</p>
                <p class="mt-1 text-sm text-slate-500">Cuando el coordinador te asigne una práctica, aparecerá aquí.</p>
                <a href="{{ route('student.offers.view') }}" class="mt-4 inline-flex rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Explorar ofertas</a>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <style>
        /* Estrellas de la evaluación */
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label { color: #f59e0b; }
    </style>
@endpush
