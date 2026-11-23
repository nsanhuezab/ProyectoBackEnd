@extends('layouts.app')

@section('title', 'Carreras')

@section('content')
    <div class="space-y-6">

        {{-- Ruta de navegación --}}
        <nav class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500" aria-label="Ruta">
            <span>Panel de administración</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="font-medium text-slate-800">Carreras</span>
        </nav>

        {{-- Encabezado --}}
        <header class="flex flex-col gap-4 border-b-2 border-slate-800/80 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">Panel de administración</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">Carreras</h1>
                <p class="mt-1 text-sm text-slate-500">Gestiona las carreras y la ponderación de evaluación de sus prácticas.</p>
            </div>

            <button type="button" onclick="openCreateCareer()"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva carrera
            </button>
        </header>

        {{-- Tarjetas resumen --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total de carreras</p>
                <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $counts['total'] }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Activas</p>
                <p class="mt-1 text-2xl font-extrabold text-emerald-700">{{ $counts['activas'] }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Inactivas</p>
                <p class="mt-1 text-2xl font-extrabold text-slate-500">{{ $counts['inactivas'] }}</p>
            </div>
        </div>

        {{-- Tabla de carreras --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="flex flex-col gap-4 border-b border-gray-100 p-4 sm:p-5 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 text-red-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[15px] font-bold text-slate-800">Listado de carreras</h2>
                        <p class="text-[12px] text-slate-500">Registradas: <strong class="text-slate-700">{{ $counts['total'] }}</strong></p>
                    </div>
                </div>

                <div class="relative w-full md:max-w-sm">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="careerSearch" placeholder="Buscar por código o nombre..."
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-[13px] text-slate-800 placeholder-slate-400 focus:bg-white focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100 transition">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="careersTable" class="w-full lg:min-w-[860px] text-left text-[13px] text-slate-600">
                    <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-6 py-3.5">Código</th>
                            <th class="px-6 py-3.5">Carrera</th>
                            <th class="px-6 py-3.5">Ponderación</th>
                            <th class="px-6 py-3.5 text-center">Estudiantes</th>
                            <th class="px-6 py-3.5 text-center">Ofertas</th>
                            <th class="px-6 py-3.5 text-center">Estado</th>
                            <th class="px-6 py-3.5 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($careers as $career)
                            @php
                                $supervisor = (float) $career->supervisor;
                                $coordinador = (float) $career->coordinador;
                                $careerData = [
                                    'id' => $career->id,
                                    'codigo' => $career->codigo,
                                    'nombre' => $career->nombre,
                                    'supervisor' => $supervisor,
                                    'coordinador' => $coordinador,
                                    'activa' => $career->activa,
                                    'students' => $career->students_count,
                                    'offers' => $career->offers_count,
                                    'users' => $career->users_count,
                                    'created' => $career->created_at?->format('d-m-Y'),
                                    'updateUrl' => route('careers.update', $career),
                                ];
                            @endphp
                            <tr class="transition-colors hover:bg-gray-50/70">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono text-[11.5px] font-bold text-slate-700">
                                        {{ $career->codigo }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-800 text-[13.5px]">
                                    {{ $career->nombre }}
                                </td>

                                <td class="px-6 py-4" data-order="{{ $supervisor }}">
                                    <div class="w-44">
                                        <div class="flex h-2 overflow-hidden rounded-full bg-slate-100">
                                            <div class="bg-red-700" style="width: {{ $supervisor }}%"></div>
                                            <div class="bg-slate-700" style="width: {{ $coordinador }}%"></div>
                                        </div>
                                        <div class="mt-1.5 flex justify-between text-[11px] font-semibold">
                                            <span class="text-red-700">Sup. {{ $supervisor + 0 }}%</span>
                                            <span class="text-slate-700">Coord. {{ $coordinador + 0 }}%</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center" data-order="{{ $career->students_count }}">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $career->students_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $career->students_count }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center" data-order="{{ $career->offers_count }}">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $career->offers_count > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $career->offers_count }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($career->activa)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactiva
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick='openShowCareer(@json($careerData))' title="Ver detalle"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-50 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button type="button" onclick='openEditCareer(@json($careerData))' title="Editar carrera"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 shadow-sm hover:bg-slate-100 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form id="delete-career-{{ $career->id }}" action="{{ route('careers.destroy', $career) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick='confirmDeleteCareer({{ $career->id }}, @json($career->nombre))' title="Eliminar carrera"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 bg-white text-red-600 shadow-sm hover:bg-red-50 transition cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal: Nueva carrera --}}
    <div id="createCareerModal" class="career-modal hidden fixed inset-0 z-[60] items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <div class="w-full max-w-xl max-h-[92vh] overflow-y-auto rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
                <div>
                    <h2 class="font-bold">Nueva carrera</h2>
                    <p class="text-[12px] text-slate-400">Registra una carrera y su ponderación de evaluación.</p>
                </div>
                <button type="button" onclick="closeCareerModal('createCareerModal')" class="text-slate-300 hover:text-white cursor-pointer" aria-label="Cerrar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('careers.store') }}" method="POST" class="space-y-5 p-6">
                @csrf
                @include('careers.partials.form-fields', ['prefix' => 'create'])

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeCareerModal('createCareerModal')"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
                    <button type="submit"
                        class="rounded-lg bg-[#C8102E] px-4 py-2 text-sm font-semibold text-white hover:bg-[#9e0b22] cursor-pointer">Guardar carrera</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Editar carrera --}}
    <div id="editCareerModal" class="career-modal hidden fixed inset-0 z-[60] items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <div class="w-full max-w-xl max-h-[92vh] overflow-y-auto rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
                <div>
                    <h2 class="font-bold">Editar carrera</h2>
                    <p id="editCareerSubtitle" class="text-[12px] text-slate-400">Modifica los datos de la carrera.</p>
                </div>
                <button type="button" onclick="closeCareerModal('editCareerModal')" class="text-slate-300 hover:text-white cursor-pointer" aria-label="Cerrar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editCareerForm" action="{{ old('_modal') === 'edit' && old('_career_id') ? route('careers.update', old('_career_id')) : '#' }}" method="POST" class="space-y-5 p-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="_career_id" id="edit-career-id" value="{{ old('_career_id') }}">
                @include('careers.partials.form-fields', ['prefix' => 'edit'])

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeCareerModal('editCareerModal')"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
                    <button type="submit"
                        class="rounded-lg bg-[#C8102E] px-4 py-2 text-sm font-semibold text-white hover:bg-[#9e0b22] cursor-pointer">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Detalle de carrera --}}
    <div id="showCareerModal" class="career-modal hidden fixed inset-0 z-[60] items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <div class="w-full max-w-md rounded-xl bg-white shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
                <div>
                    <p id="showCareerCode" class="font-mono text-[11px] font-bold text-red-300"></p>
                    <h2 id="showCareerName" class="font-bold"></h2>
                </div>
                <button type="button" onclick="closeCareerModal('showCareerModal')" class="text-slate-300 hover:text-white cursor-pointer" aria-label="Cerrar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <dl class="divide-y divide-slate-100 px-6 py-2 text-[13px]">
                <div class="flex justify-between py-3"><dt class="text-slate-500">Estado</dt><dd id="showCareerStatus" class="font-semibold text-slate-800"></dd></div>
                <div class="flex justify-between py-3"><dt class="text-slate-500">Ponderación supervisor</dt><dd id="showCareerSupervisor" class="font-semibold text-red-700"></dd></div>
                <div class="flex justify-between py-3"><dt class="text-slate-500">Ponderación coordinador</dt><dd id="showCareerCoordinator" class="font-semibold text-slate-800"></dd></div>
                <div class="flex justify-between py-3"><dt class="text-slate-500">Estudiantes</dt><dd id="showCareerStudents" class="font-semibold text-slate-800"></dd></div>
                <div class="flex justify-between py-3"><dt class="text-slate-500">Ofertas asociadas</dt><dd id="showCareerOffers" class="font-semibold text-slate-800"></dd></div>
                <div class="flex justify-between py-3"><dt class="text-slate-500">Usuarios (coordinadores)</dt><dd id="showCareerUsers" class="font-semibold text-slate-800"></dd></div>
                <div class="flex justify-between py-3"><dt class="text-slate-500">Registrada el</dt><dd id="showCareerCreated" class="font-semibold text-slate-800"></dd></div>
            </dl>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        #careersTable_wrapper .dataTables_info { padding: 1rem 1.5rem; font-size: 12.5px; color: #64748b; }
        #careersTable_wrapper .dataTables_paginate { padding: 0.75rem 1.5rem; font-size: 12.5px; }
        #careersTable_wrapper .dataTables_paginate .paginate_button.current,
        #careersTable_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0f172a; color: #fff !important; border-color: #0f172a; border-radius: 0.5rem;
        }
        table.dataTable.no-footer { border-bottom: 0; }
        table.dataTable thead th { border-bottom: 1px solid #f1f5f9; }
    </style>

    <script>
        // Tabla de carreras
        const careersTable = $('#careersTable').DataTable({
            dom: 'rtip',
            pageLength: 10,
            order: [[1, 'asc']],
            columnDefs: [{ targets: -1, orderable: false, searchable: false }],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                emptyTable: 'Aún no hay carreras registradas. Crea la primera con «Nueva carrera».',
            },
        });

        document.getElementById('careerSearch').addEventListener('input', (event) => {
            careersTable.search(event.target.value).draw();
        });

        // Modales
        function openCareerModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCareerModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.querySelectorAll('.career-modal').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeCareerModal(modal.id);
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                document.querySelectorAll('.career-modal.flex').forEach((modal) => closeCareerModal(modal.id));
            }
        });

        function openCreateCareer() {
            openCareerModal('createCareerModal');
            document.getElementById('create-codigo').focus();
        }

        function openEditCareer(career) {
            const form = document.getElementById('editCareerForm');
            form.action = career.updateUrl;
            form.querySelector('[role="alert"]')?.remove();

            document.getElementById('edit-career-id').value = career.id;
            document.getElementById('edit-codigo').value = career.codigo;
            document.getElementById('edit-nombre').value = career.nombre;
            document.getElementById('edit-supervisor').value = career.supervisor;
            document.getElementById('edit-coordinador').value = career.coordinador;
            document.getElementById('edit-activa').checked = Boolean(career.activa);
            document.getElementById('editCareerSubtitle').textContent = `${career.codigo} · ${career.nombre}`;

            openCareerModal('editCareerModal');
        }

        function openShowCareer(career) {
            document.getElementById('showCareerCode').textContent = career.codigo;
            document.getElementById('showCareerName').textContent = career.nombre;
            document.getElementById('showCareerStatus').textContent = career.activa ? 'Activa' : 'Inactiva';
            document.getElementById('showCareerSupervisor').textContent = `${career.supervisor}%`;
            document.getElementById('showCareerCoordinator').textContent = `${career.coordinador}%`;
            document.getElementById('showCareerStudents').textContent = career.students;
            document.getElementById('showCareerOffers').textContent = career.offers;
            document.getElementById('showCareerUsers').textContent = career.users;
            document.getElementById('showCareerCreated').textContent = career.created ?? '—';

            openCareerModal('showCareerModal');
        }

        // Supervisor + coordinador = 100
        document.querySelectorAll('[data-weight]').forEach((input) => {
            input.addEventListener('input', () => {
                const other = input.dataset.weight === 'supervisor' ? 'coordinador' : 'supervisor';
                const value = parseFloat(input.value);
                if (!Number.isNaN(value) && value >= 0 && value <= 100) {
                    document.getElementById(`${input.dataset.prefix}-${other}`).value = +(100 - value).toFixed(2);
                }
            });
        });

        // Confirmar eliminación
        function confirmDeleteCareer(id, name) {
            Swal.fire({
                title: '¿Eliminar carrera?',
                html: `Se eliminará <strong>${$('<div>').text(name).html()}</strong>. Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C8102E',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-career-${id}`).submit();
                }
            });
        }

        // Reabrir el modal si hubo errores
        @if ($errors->any() && old('_modal') === 'create')
            openCareerModal('createCareerModal');
        @elseif ($errors->any() && old('_modal') === 'edit')
            openCareerModal('editCareerModal');
        @endif
    </script>
@endpush
