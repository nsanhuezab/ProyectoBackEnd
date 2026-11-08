@extends('layouts.app')

@section('title', $isCompany ? 'Mis ofertas' : 'Ofertas de práctica')

@section('content')
    <div class="space-y-6">
        <x-page-header
            :title="$isCompany ? 'Mis ofertas' : 'Ofertas de práctica'"
            :section="$isCompany ? 'Área empresa' : 'Panel de administración'"
            :description="$can['manage'] ? 'Publica ofertas de práctica. Cada oferta nueva o editada queda pendiente hasta que se apruebe.' : 'Revisa las ofertas de tu carrera y apruébalas para que los estudiantes puedan postular.'">
            @if ($can['manage'] && (! $isCompany || $ownCompany))
                <x-slot:actions>
                    <button type="button" onclick="openCreateModal('createOfferModal')"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nueva oferta
                    </button>
                </x-slot:actions>
            @endif
        </x-page-header>

        @if ($isCompany && ! $ownCompany)
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-[13px] text-amber-800">
                Tu cuenta todavía no está vinculada a una empresa. Pide al administrador que la asocie desde el módulo Empresas para poder publicar ofertas.
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card label="Ofertas" :value="$counts['total']" />
            <x-stat-card label="Pendientes" :value="$counts['pendientes']" color="amber" />
            <x-stat-card label="Aprobadas" :value="$counts['aprobadas']" color="emerald" />
            <x-stat-card label="Rechazadas" :value="$counts['rechazadas']" color="red" />
        </div>

        <x-table-card title="Listado de ofertas" :total="$counts['total']" search-id="offerSearch" search-placeholder="Buscar por título, empresa, carrera o estado...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </x-slot:icon>

            <table id="offersTable" class="w-full min-w-[1000px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Oferta</th>
                        <th class="px-6 py-3.5">Empresa</th>
                        <th class="px-6 py-3.5">Carreras</th>
                        <th class="px-6 py-3.5">Cupos asignados</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($offers as $offer)
                        @php
                            $skills = $offer->habilidades ?? [];
                            $vacancies = max((int) $offer->vacantes, 1);
                            $filled = min($offer->assigned_count, $vacancies);
                            $companyName = $offer->company?->razon_social ?? $offer->empresa;
                            $rowData = [
                                'titulo' => $offer->titulo,
                                'empresa_id' => $offer->empresa_id,
                                'career_ids' => $offer->careers->pluck('id')->all(),
                                'area' => $offer->area,
                                'perfil' => $offer->perfil,
                                'nivel_requerido' => $offer->nivel_requerido,
                                'modalidad' => $offer->modalidad,
                                'duracion_semanas' => $offer->duracion_semanas,
                                'horario' => $offer->horario,
                                'remuneracion' => $offer->remuneracion,
                                'vacantes' => $offer->vacantes,
                                'habilidades' => implode(', ', $skills),
                                'ubicacion' => $offer->ubicacion,
                            ];
                            $showData = [
                                'titulo' => $offer->titulo,
                                'empresa' => $companyName,
                                'estado' => $offer->estado,
                                'carreras' => $offer->careers->pluck('nombre')->implode(', '),
                                'area' => $offer->area,
                                'modalidad' => $offer->modalidad,
                                'ubicacion' => $offer->ubicacion,
                                'duracion' => $offer->duracion_semanas ? $offer->duracion_semanas.' semanas' : null,
                                'horario' => $offer->horario,
                                'remuneracion' => $offer->remuneracion !== null ? '$'.number_format((float) $offer->remuneracion, 0, ',', '.') : 'No informada',
                                'cupos' => $offer->assigned_count.' de '.$offer->vacantes.' asignados · '.$offer->applications_count.' postulación(es)',
                                'nivel_requerido' => $offer->nivel_requerido,
                                'habilidades' => implode(', ', $skills),
                                'perfil' => $offer->perfil,
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-[13.5px]">{{ $offer->titulo }}</p>
                                <p class="text-[12px] text-slate-500">{{ collect([$offer->area, $offer->modalidad, $offer->ubicacion])->filter()->implode(' · ') }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $companyName }}</td>
                            <td class="px-6 py-4" data-search="{{ $offer->careers->pluck('nombre')->implode(' ') }}">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($offer->careers as $career)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono text-[11px] font-bold text-slate-700" title="{{ $career->nombre }}">{{ $career->codigo }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4" data-order="{{ $offer->vacantes - $offer->assigned_count }}">
                                <div class="w-36">
                                    <div class="flex justify-between text-[11.5px] font-semibold text-slate-600 mb-1">
                                        <span>{{ $offer->assigned_count }} / {{ $offer->vacantes }}</span>
                                        <span class="text-slate-400">{{ $offer->applications_count }} post.</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full {{ $filled >= $vacancies ? 'bg-red-700' : 'bg-emerald-500' }}" style="width: {{ round($filled / $vacancies * 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-status-badge :status="$offer->estado" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-action-button type="view" title="Ver detalle"
                                        onclick="openShowModal('showOfferModal', {{ json_encode($showData) }})" />

                                    @if ($can['approve'] && $offer->estado === 'Pendiente')
                                        <form id="review-offer-{{ $offer->id }}" action="{{ route('offers.approve', $offer) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="estado" value="">
                                            <x-action-button type="approve" title="Aprobar o rechazar"
                                                onclick="reviewOffer('review-offer-{{ $offer->id }}', {{ json_encode($offer->titulo) }})" />
                                        </form>
                                    @endif

                                    @if ($can['manage'])
                                        <x-action-button type="edit" title="Editar oferta"
                                            onclick="openEditModal('editOfferModal', {{ json_encode(route('offers.update', $offer)) }}, {{ json_encode($rowData) }}, {{ json_encode($offer->titulo) }})" />
                                        <form id="delete-offer-{{ $offer->id }}" action="{{ route('offers.destroy', $offer) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-action-button type="delete" title="Eliminar oferta"
                                                onclick="confirmSubmit('delete-offer-{{ $offer->id }}', { title: '¿Eliminar oferta?', text: {{ json_encode('Se eliminará «'.$offer->titulo.'». Esta acción no se puede deshacer.') }}, confirmText: 'Sí, eliminar' })" />
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

    {{-- Modal: detalle de la oferta --}}
    <x-form-modal id="showOfferModal" title="Detalle de la oferta" width="max-w-2xl">
        <div class="px-6 py-5 space-y-4 text-[13px]">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-red-700" data-show="empresa"></p>
                <h3 class="text-lg font-extrabold text-slate-900" data-show="titulo"></h3>
                <p class="mt-1 text-slate-500">Estado: <strong class="text-slate-800" data-show="estado"></strong></p>
            </div>
            @php
                $detailFields = [
                    'carreras' => 'Carreras', 'area' => 'Área', 'modalidad' => 'Modalidad', 'ubicacion' => 'Ubicación',
                    'duracion' => 'Duración', 'horario' => 'Horario', 'remuneracion' => 'Remuneración', 'cupos' => 'Cupos',
                    'nivel_requerido' => 'Nivel requerido', 'habilidades' => 'Habilidades',
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
                <p class="text-[11.5px] text-slate-500 mb-1">Perfil requerido</p>
                <p class="whitespace-pre-line text-slate-700 leading-relaxed" data-show="perfil"></p>
            </div>
        </div>
    </x-form-modal>

    @if ($can['manage'])
        <datalist id="nivelesRequeridos">
            <option value="Inicial"></option>
            <option value="Intermedio"></option>
            <option value="Avanzado"></option>
        </datalist>

        @foreach (['create' => 'Nueva oferta', 'edit' => 'Editar oferta'] as $mode => $modalTitle)
            @php $modalId = $mode.'OfferModal'; @endphp
            <x-form-modal :id="$modalId" :title="$modalTitle" :subtitle="$mode === 'edit' ? 'Al guardar, la oferta vuelve a quedar pendiente de aprobación.' : 'La oferta quedará pendiente hasta que se apruebe.'" width="max-w-3xl">
                <form action="{{ $mode === 'create' ? route('offers.store') : old('_action', '#') }}" method="POST" class="space-y-5 p-6">
                    @csrf
                    @if ($mode === 'edit') @method('PUT') @endif
                    <input type="hidden" name="_modal" value="{{ $modalId }}">
                    <input type="hidden" name="_action" value="{{ old('_action') }}">
                    <x-form-errors :modal="$modalId" />

                    @php
                        $old = fn (string $key, $default = null) => old('_modal') === $modalId ? old($key, $default) : $default;
                        $oldCareers = collect($old('career_ids', []))->map(fn ($id) => (string) $id)->all();
                        $oldSkills = $old('habilidades');
                    @endphp

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-field label="Título de la oferta" for="{{ $mode }}-titulo" required class="{{ $can['chooseCompany'] ? '' : 'sm:col-span-2' }}">
                            <input type="text" id="{{ $mode }}-titulo" name="titulo" required maxlength="255" value="{{ $old('titulo') }}" placeholder="Ej: Desarrollador Backend Laravel" class="form-input">
                        </x-field>
                        @if ($can['chooseCompany'])
                            <x-field label="Empresa" for="{{ $mode }}-empresa_id" required>
                                <select id="{{ $mode }}-empresa_id" name="empresa_id" required class="form-input">
                                    <option value="">Selecciona una empresa</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" @selected((string) $old('empresa_id') === (string) $company->id)>{{ $company->razon_social }}</option>
                                    @endforeach
                                </select>
                            </x-field>
                        @endif
                    </div>

                    <x-field label="Carreras a las que va dirigida" required>
                        <div class="grid gap-2 sm:grid-cols-2 rounded-lg border border-slate-200 bg-slate-50/60 p-3">
                            @foreach ($careers as $career)
                                <label class="flex items-center gap-2.5 text-[13px] text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="career_ids[]" value="{{ $career->id }}" @checked(in_array((string) $career->id, $oldCareers, true))
                                        class="h-4 w-4 rounded border-slate-300 text-red-700 focus:ring-red-600">
                                    <span><span class="font-mono text-[11px] font-bold text-slate-500">{{ $career->codigo }}</span> {{ $career->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                    </x-field>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <x-field label="Área" for="{{ $mode }}-area" required>
                            <input type="text" id="{{ $mode }}-area" name="area" required maxlength="120" value="{{ $old('area') }}" placeholder="Desarrollo de software" class="form-input">
                        </x-field>
                        <x-field label="Modalidad" for="{{ $mode }}-modalidad" required>
                            <select id="{{ $mode }}-modalidad" name="modalidad" required class="form-input">
                                <option value="">Selecciona</option>
                                @foreach (['Presencial', 'Remota', 'Híbrida'] as $modality)
                                    <option value="{{ $modality }}" @selected($old('modalidad') === $modality)>{{ $modality }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="Ubicación" for="{{ $mode }}-ubicacion" required>
                            <input type="text" id="{{ $mode }}-ubicacion" name="ubicacion" required maxlength="255" value="{{ $old('ubicacion') }}" placeholder="Concepción" class="form-input">
                        </x-field>
                        <x-field label="Cupos" for="{{ $mode }}-vacantes" required>
                            <input type="number" id="{{ $mode }}-vacantes" name="vacantes" required min="1" max="1000" value="{{ $old('vacantes', 1) }}" class="form-input">
                        </x-field>
                        <x-field label="Duración (semanas)" for="{{ $mode }}-duracion_semanas" required>
                            <input type="number" id="{{ $mode }}-duracion_semanas" name="duracion_semanas" required min="1" max="104" value="{{ $old('duracion_semanas') }}" class="form-input">
                        </x-field>
                        <x-field label="Remuneración (CLP)" for="{{ $mode }}-remuneracion">
                            <input type="number" id="{{ $mode }}-remuneracion" name="remuneracion" min="0" step="1" value="{{ $old('remuneracion') }}" placeholder="Opcional" class="form-input">
                        </x-field>
                        <x-field label="Horario" for="{{ $mode }}-horario" required>
                            <input type="text" id="{{ $mode }}-horario" name="horario" required maxlength="255" value="{{ $old('horario') }}" placeholder="Lunes a viernes, 9:00 a 18:00" class="form-input">
                        </x-field>
                        <x-field label="Nivel requerido" for="{{ $mode }}-nivel_requerido" required>
                            <input type="text" id="{{ $mode }}-nivel_requerido" name="nivel_requerido" list="nivelesRequeridos" required maxlength="120" value="{{ $old('nivel_requerido') }}" class="form-input">
                        </x-field>
                        <x-field label="Habilidades requeridas" for="{{ $mode }}-habilidades" required hint="Separadas por comas.">
                            <input type="text" id="{{ $mode }}-habilidades" name="habilidades" required value="{{ is_array($oldSkills) ? implode(', ', $oldSkills) : $oldSkills }}" placeholder="PHP, SQL, Git" class="form-input">
                        </x-field>
                    </div>

                    <x-field label="Perfil requerido" for="{{ $mode }}-perfil" required>
                        <textarea id="{{ $mode }}-perfil" name="perfil" rows="4" required maxlength="5000" class="form-input" placeholder="Describe las tareas y lo que se espera del estudiante.">{{ $old('perfil') }}</textarea>
                    </x-field>

                    <x-form-actions :modal="$modalId" :label="$mode === 'create' ? 'Publicar oferta' : 'Guardar cambios'" />
                </form>
            </x-form-modal>
        @endforeach
    @endif
@endsection

@push('scripts')
    <script>
        initDataTable('#offersTable', '#offerSearch', { order: [] });

        // Aprobar / rechazar con SweetAlert2: se rellena el campo "estado" y se envía el formulario.
        function reviewOffer(formId, title) {
            Swal.fire({
                title: 'Revisar oferta',
                text: `¿Qué decides sobre «${title}»?`,
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Aprobar',
                denyButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#047857',
                denyButtonColor: '#C8102E',
            }).then((result) => {
                if (result.isDismissed) return;
                const form = document.getElementById(formId);
                form.querySelector('[name="estado"]').value = result.isConfirmed ? 'Aprobada' : 'Rechazada';
                form.submit();
            });
        }
    </script>
@endpush
