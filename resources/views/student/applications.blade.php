@extends('layouts.app')

@section('title', 'Mis postulaciones')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Mis postulaciones" section="Área estudiante"
            description="Revisa el estado de cada postulación, descarga tu CV y conversa con la coordinación." />

        <x-table-card title="Postulaciones enviadas" :total="$applications->count()" search-id="myApplicationSearch" search-placeholder="Buscar por oferta, empresa o estado...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </x-slot:icon>

            <table id="myApplicationsTable" class="w-full lg:min-w-[820px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
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
                            $score = (float) $application->matching_score;
                            $scoreColor = $score >= 70 ? 'bg-emerald-500' : ($score >= 40 ? 'bg-amber-500' : 'bg-red-600');
                            $messages = $application->messages->count();
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-[13.5px]">{{ $application->offer?->titulo }}</p>
                                <p class="text-[12px] text-slate-500">{{ $application->offer?->company?->razon_social ?? $application->offer?->empresa }}{{ $application->offer?->modalidad ? ' · '.$application->offer->modalidad : '' }}</p>
                            </td>
                            <td class="px-6 py-4" data-order="{{ (int) round($score * 100) }}">
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
                                    {{-- Ver detalle y mensajes --}}
                                    <a href="{{ route('applications.show', $application) }}" title="Ver detalle y mensajes"
                                        class="relative inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                        @if ($messages > 0)
                                            <span class="absolute -top-1.5 -right-1.5 min-w-[16px] h-4 px-1 rounded-full bg-red-700 text-[9.5px] font-bold text-white flex items-center justify-center">{{ $messages }}</span>
                                        @endif
                                    </a>

                                    {{-- Descargar CV --}}
                                    @if ($application->cv_path)
                                        <a href="{{ route('applications.cv', $application) }}" title="Descargar mi CV"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 shadow-sm hover:bg-red-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                    @endif

                                    {{-- Retirar --}}
                                    @if ($application->status === 'Postulado')
                                        <form id="withdraw-application-{{ $application->id }}" action="{{ route('student.applications.destroy', $application) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-action-button type="delete" title="Retirar postulación"
                                                onclick="confirmSubmit('withdraw-application-{{ $application->id }}', { title: '¿Retirar postulación?', text: {{ json_encode('Se retirará tu postulación a «'.$application->offer?->titulo.'» y se borrará el CV adjunto.') }}, confirmText: 'Sí, retirar' })" />
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
@endsection

@push('scripts')
    <script>
        initDataTable('#myApplicationsTable', '#myApplicationSearch', {
            order: [[3, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                emptyTable: 'Aún no has postulado a ninguna oferta. Revisa «Explorar ofertas».',
            },
        });
    </script>
@endpush
