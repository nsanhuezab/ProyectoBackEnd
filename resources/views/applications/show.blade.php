@extends('layouts.app')

@php
    $studentUser = $application->student?->user;
    $studentName = trim(($studentUser?->name ?? '').' '.($studentUser?->last_name ?? '')) ?: 'Estudiante';
    $offer = $application->offer;
    $score = (float) $application->matching_score;
@endphp

@section('title', 'Postulación de '.$studentName)

@section('content')
    <div class="space-y-6">
        <x-page-header :title="$isStudent ? 'Mi postulación' : 'Postulación de '.$studentName"
            :section="$isStudent ? 'Área estudiante' : 'Postulaciones'"
            :description="($offer?->titulo ?? '').' · '.($offer?->company?->razon_social ?? $offer?->empresa)">
            <x-slot:actions>
                <a href="{{ $isStudent ? route('student.applications.view') : route('applications.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Volver</a>
                @if ($application->practice && ! $isStudent)
                    <a href="{{ route('practices.show', $application->practice) }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Ver práctica</a>
                @endif
            </x-slot:actions>
        </x-page-header>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Resumen de la postulación --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-5 space-y-4 text-[13px]">
                <div class="flex items-center justify-between">
                    <h2 class="text-[15px] font-bold text-slate-800">Resumen</h2>
                    <x-status-badge :status="$application->status" />
                </div>
                <dl class="space-y-3">
                    <div><dt class="text-[11.5px] text-slate-500">Estudiante</dt><dd class="font-semibold text-slate-800">{{ $studentName }} · {{ $studentUser?->email }}</dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Carrera</dt><dd class="font-semibold text-slate-800">{{ $application->student?->career?->nombre ?? '—' }}</dd></div>
                    <div>
                        <dt class="text-[11.5px] text-slate-500">Afinidad con la oferta</dt>
                        <dd class="flex items-center gap-2">
                            <div class="h-2 flex-1 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full {{ $score >= 70 ? 'bg-emerald-500' : ($score >= 40 ? 'bg-amber-500' : 'bg-red-600') }}" style="width: {{ min($score, 100) }}%"></div>
                            </div>
                            <span class="font-bold text-slate-800">{{ round($score) }}%</span>
                        </dd>
                    </div>
                    <div><dt class="text-[11.5px] text-slate-500">Postuló el</dt><dd class="font-semibold text-slate-800">{{ $application->created_at?->format('d-m-Y H:i') }}</dd></div>
                    @if ($application->decision_at)
                        <div><dt class="text-[11.5px] text-slate-500">Resuelta el</dt><dd class="font-semibold text-slate-800">{{ $application->decision_at->format('d-m-Y H:i') }}{{ $application->reviewer ? ' por '.trim($application->reviewer->name.' '.$application->reviewer->last_name) : '' }}</dd></div>
                    @endif
                </dl>

                <div>
                    <p class="text-[11.5px] text-slate-500 mb-1">Carta de motivación</p>
                    <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $application->motivation }}</p>
                </div>

                <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    @if ($application->cv_path)
                        <a href="{{ route('applications.cv', $application) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-[12.5px] font-semibold text-slate-700 hover:bg-slate-50">Descargar CV</a>
                    @endif
                    @if ($canReview)
                        <form id="assign-application" action="{{ route('applications.assign', $application) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmSubmit('assign-application', { title: '¿Asignar práctica?', text: 'Se creará la práctica y se avisará al estudiante.', confirmText: 'Sí, asignar', color: '#047857', icon: 'question' })"
                                class="rounded-lg bg-emerald-700 px-3 py-2 text-[12.5px] font-semibold text-white hover:bg-emerald-800 cursor-pointer">Asignar práctica</button>
                        </form>
                        <form id="reject-application" action="{{ route('applications.reject', $application) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmSubmit('reject-application', { title: '¿Rechazar postulación?', text: 'Se avisará al estudiante.', confirmText: 'Sí, rechazar' })"
                                class="rounded-lg border border-red-200 px-3 py-2 text-[12.5px] font-semibold text-red-700 hover:bg-red-50 cursor-pointer">Rechazar</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Conversación (RF-03) --}}
            <div class="lg:col-span-2 rounded-xl bg-white shadow-sm ring-1 ring-gray-200 flex flex-col">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-[15px] font-bold text-slate-800">Mensajes de la postulación</h2>
                    <p class="text-[12px] text-slate-500">Conversación entre el estudiante y la coordinación. Cada mensaje guarda el estado que tenía la postulación.</p>
                </div>

                <div class="flex-1 space-y-3 p-5 max-h-[460px] overflow-y-auto" id="messages">
                    @forelse ($application->messages as $message)
                        @php $mine = $message->user_id === auth()->id(); @endphp
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-xl px-4 py-2.5 {{ $mine ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800' }}">
                                <p class="text-[11px] font-semibold {{ $mine ? 'text-slate-300' : 'text-slate-500' }}">
                                    {{ $message->user ? trim($message->user->name.' '.$message->user->last_name) : 'Usuario eliminado' }}
                                    · {{ $message->created_at?->format('d-m-Y H:i') }} · <span class="italic">{{ $message->application_status }}</span>
                                </p>
                                <p class="mt-0.5 text-[13px] whitespace-pre-line">{{ $message->body }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-[13px] text-slate-500 py-10">Aún no hay mensajes. Escribe el primero para resolver dudas sobre esta postulación.</p>
                    @endforelse
                </div>

                <form action="{{ route('applications.messages.store', $application) }}" method="POST" class="border-t border-gray-100 p-4 flex gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <label for="message-body" class="sr-only">Mensaje</label>
                        <textarea id="message-body" name="body" rows="2" required maxlength="2000" placeholder="Escribe un mensaje..." class="form-input">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="mt-1 text-[12px] text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="rounded-lg bg-[#C8102E] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#9e0b22] cursor-pointer">Enviar</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const messages = document.getElementById('messages');
        messages.scrollTop = messages.scrollHeight;
    </script>
@endpush
