@extends('layouts.app')

@section('title', $config['title'])

@section('content')
    <section class="space-y-6">
        <header class="flex flex-col gap-4 border-b-2 border-slate-800/80 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">Panel de administración</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $config['title'] }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">{{ $config['description'] }}</p>
            </div>

            @if ($config['canCreate'] && count($config['fields']))
                <button type="button" id="createRecord"
                    class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                    <i class="fa-solid fa-circle-plus mr-2" aria-hidden="true"></i>
                    Agregar registro
                </button>
            @endif
        </header>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="overflow-x-auto">
                <table id="resourceTable" class="min-w-full divide-y divide-gray-100 text-sm text-slate-700">
                    <thead class="bg-gray-50/50">
                        <tr>
                            @foreach ($config['columns'] as $column)
                                <th scope="col"
                                    class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    {{ $column['title'] }}
                                </th>
                            @endforeach
                            <th scope="col"
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>

    @if ($config['canCreate'] && count($config['fields']))
        <div id="recordModal"
            class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/60 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200"
            role="dialog" aria-modal="true" aria-labelledby="recordModalTitle">
            <div id="recordModalPanel"
                class="max-h-[92vh] w-full max-w-3xl scale-95 overflow-y-auto rounded-xl bg-white shadow-2xl transition-transform duration-200">
                <div class="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
                    <h2 id="recordModalTitle" class="font-bold">Nuevo registro</h2>
                    <button type="button" id="closeRecordModal" class="text-slate-300 hover:text-white"
                        aria-label="Cerrar">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form id="recordForm" class="space-y-5 p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($config['fields'] as $field)
                            <div class="{{ $field['type'] === 'textarea' ? 'sm:col-span-2' : '' }}">
                                <label for="field-{{ $field['name'] }}"
                                    class="mb-1 block text-xs font-semibold text-slate-700">
                                    {{ $field['label'] }}
                                </label>

                                @if ($field['type'] === 'select')
                                    <select id="field-{{ $field['name'] }}" name="{{ $field['name'] }}"
                                        @if ($field['multiple'] ?? false) multiple size="4" @endif
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                                        @unless ($field['multiple'] ?? false)
                                            <option value="">Selecciona una opción</option>
                                        @endunless
                                        @foreach ($field['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field['type'] === 'textarea')
                                    <textarea id="field-{{ $field['name'] }}" name="{{ $field['name'] }}" rows="3"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100"></textarea>
                                @elseif ($field['type'] === 'checkbox')
                                    <input id="field-{{ $field['name'] }}" name="{{ $field['name'] }}"
                                        type="checkbox" value="1"
                                        class="h-4 w-4 rounded border-slate-300 text-red-700 focus:ring-red-600">
                                @else
                                    <input id="field-{{ $field['name'] }}" name="{{ $field['name'] }}"
                                        type="{{ $field['type'] }}"
                                        @foreach ($field['attributes'] ?? [] as $attribute => $value)
                                            {{ $attribute }}="{{ $value }}"
                                        @endforeach
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <p id="recordFormError"
                        class="hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800"
                        role="alert"></p>

                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" id="cancelRecord"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-[#C8102E] px-4 py-2 text-sm font-semibold text-white hover:bg-[#9e0b22]">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const config = @js($config);
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const columnNames = config.columns.map((column) => column.data);

            const icons = {
                edit: `
                    <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>`,
                delete: `
                    <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>`,
                check: `
                    <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m5 12 4 4L19 6" />
                    </svg>`,
                reject: `
                    <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>`,
                next: `
                    <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.5 12h15m-6-6 6 6-6 6" />
                    </svg>`,
            };

            const iconButton = (action, title, icon, colorClasses, extraAttributes = '') => `
                <button type="button"
                    data-action="${action}"
                    title="${title}"
                    aria-label="${title}"
                    ${extraAttributes}
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border bg-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-1 ${colorClasses}">
                    ${icon}
                </button>
            `;

            const table = $('#resourceTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                dom: 'rtip',
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },

                ajax: async (request, callback) => {
                    const order = request.order?.[0] ?? {
                        column: 0,
                        dir: 'asc'
                    };

                    const params = new URLSearchParams({
                        page: String(Math.floor(request.start / request.length) + 1),
                        per_page: String(request.length),
                        search: request.search.value,
                        sort: columnNames[order.column] ?? 'id',
                        dir: order.dir,
                    });

                    try {
                        const response = await fetch(
                            `${config.dataUrl ?? config.apiUrl}?${params}`, {
                                headers: {
                                    Accept: 'application/json'
                                }
                            }
                        );

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message ?? 'No se pudieron cargar los registros.');
                        }

                        callback({
                            draw: request.draw,
                            recordsTotal: result.meta.total,
                            recordsFiltered: result.meta.filtered,
                            data: result.data,
                        });
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de carga',
                            text: error.message
                        });

                        callback({
                            draw: request.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                    }
                },

                columns: [
                    ...config.columns.map((column) => ({
                        ...column,
                        className: 'whitespace-nowrap px-6 py-4',
                        render: (value, type) => {
                            if (column.data === 'activa') {
                                const label = value ? 'Activa' : 'Inactiva';
                                return type === 'display' ? label : value;
                            }

                            const text = Array.isArray(value) ? value.join(', ') : (value ?? '');

                            return type === 'display'
                                ? $.fn.dataTable.render.text().display(String(text))
                                : text;
                        },
                    })),
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'whitespace-nowrap px-6 py-4 text-center',
                        render: (_value, _type, row) => {
                            const actions = [];

                            if (config.fields.length) {
                                actions.push(iconButton(
                                    'edit',
                                    'Editar registro',
                                    icons.edit,
                                    'border-gray-300 text-gray-600 hover:bg-gray-50 focus:ring-gray-200'
                                ));
                            }

                            if (config.actions.includes('approval') && row.estado === 'Pendiente') {
                                actions.push(iconButton(
                                    'approval',
                                    'Revisar oferta',
                                    icons.check,
                                    'border-emerald-200 text-emerald-700 hover:bg-emerald-50 focus:ring-emerald-100'
                                ));
                            }

                            if (config.actions.includes('application') && row.estado === 'Postulado') {
                                actions.push(iconButton(
                                    'assign',
                                    'Asignar estudiante',
                                    icons.check,
                                    'border-emerald-200 text-emerald-700 hover:bg-emerald-50 focus:ring-emerald-100'
                                ));

                                actions.push(iconButton(
                                    'reject',
                                    'Rechazar postulación',
                                    icons.reject,
                                    'border-amber-200 text-amber-700 hover:bg-amber-50 focus:ring-amber-100'
                                ));
                            }

                            if (config.actions.includes('transition')) {
                                const next = {
                                    Asignado: 'En Curso',
                                    'En Curso': 'Informe Entregado',
                                    'Informe Entregado': 'Finalizada'
                                }[row.estado];

                                if (next) {
                                    actions.push(iconButton(
                                        'transition',
                                        `Cambiar estado a ${next}`,
                                        icons.next,
                                        'border-emerald-200 text-emerald-700 hover:bg-emerald-50 focus:ring-emerald-100',
                                        `data-state="${next}"`
                                    ));
                                }
                            }

                            if (config.canDelete !== false) {
                                actions.push(iconButton(
                                    'delete',
                                    'Eliminar registro',
                                    icons.delete,
                                    'border-red-200 text-red-600 hover:bg-red-50 focus:ring-red-100'
                                ));
                            }

                            return `<div class="flex items-center justify-center gap-1.5">${actions.join('')}</div>`;
                        },
                    },
                ],

                createdRow: (row) => {
                    row.classList.add('transition-colors', 'hover:bg-gray-50/60');
                },
            });

            const modal = document.getElementById('recordModal');
            const modalPanel = document.getElementById('recordModalPanel');
            const form = document.getElementById('recordForm');
            let editingId = null;

            const showModal = (row = null) => {
                if (!modal || !form) return;

                editingId = row?.id ?? null;
                form.reset();

                document.getElementById('recordModalTitle').textContent =
                    editingId ? 'Editar registro' : 'Nuevo registro';

                document.getElementById('recordFormError').classList.add('hidden');

                config.fields.forEach((field) => {
                    const input = form.elements.namedItem(field.name);
                    if (!input) return;

                    let value = row?.[field.name] ?? '';

                    if (field.type === 'tags' && Array.isArray(value)) {
                        value = value.join(', ');
                    }

                    if (input instanceof HTMLSelectElement && input.multiple) {
                        [...input.options].forEach((option) => {
                            option.selected = (row?.[field.name] ?? [])
                                .map(String)
                                .includes(option.value);
                        });
                    } else if (field.type === 'checkbox') {
                        input.checked = Boolean(value);
                    } else {
                        input.value = value;
                    }
                });

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalPanel?.classList.remove('scale-95');
                    modalPanel?.classList.add('scale-100');
                }, 10);
            };

            const hideModal = () => {
                if (!modal) return;

                modalPanel?.classList.remove('scale-100');
                modalPanel?.classList.add('scale-95');
                modal.classList.add('opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    editingId = null;
                }, 200);
            };

            document.getElementById('createRecord')?.addEventListener('click', () => showModal());
            document.getElementById('closeRecordModal')?.addEventListener('click', hideModal);
            document.getElementById('cancelRecord')?.addEventListener('click', hideModal);

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();

                const payload = {};

                config.fields.forEach((field) => {
                    const input = form.elements.namedItem(field.name);

                    if (field.type === 'checkbox') {
                        payload[field.name] = input.checked;
                    } else if (input instanceof HTMLSelectElement && input.multiple) {
                        payload[field.name] = [...input.selectedOptions].map((option) => option.value);
                    } else if (field.type === 'tags') {
                        payload[field.name] = input.value
                            .split(',')
                            .map((value) => value.trim())
                            .filter(Boolean);
                    } else {
                        payload[field.name] = input.value;
                    }
                });

                Object.assign(payload, config.defaults ?? {});

                const response = await fetch(
                    editingId ? `${config.apiUrl}/${editingId}` : config.apiUrl, {
                        method: editingId ? 'PUT' : 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify(payload),
                    }
                );

                const result = response.status === 204 ? {} : await response.json();

                if (!response.ok) {
                    const message = Object.values(result.errors ?? {}).flat().join(' ') ||
                        result.message ||
                        'No se pudo guardar el registro.';

                    const error = document.getElementById('recordFormError');
                    error.textContent = message;
                    error.classList.remove('hidden');
                    return;
                }

                hideModal();
                table.ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: 'El registro se guardó correctamente.',
                    timer: 1800,
                    showConfirmButton: false
                });
            });

            $('#resourceTable').on('click', '[data-action]', async function() {
                const row = table.row($(this).closest('tr')).data();
                if (!row) return;

                const id = row.id;
                const action = this.dataset.action;
                let url = `${config.apiUrl}/${id}`;
                let method = 'DELETE';
                let body;

                if (action === 'edit') {
                    if (config.fields.length) showModal(row);
                    return;
                }

                if (action === 'approval') {
                    const choice = await Swal.fire({
                        title: 'Revisar oferta',
                        text: '¿Apruebas esta oferta para publicarla?',
                        icon: 'question',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'Aprobar',
                        denyButtonText: 'Rechazar',
                        cancelButtonText: 'Cancelar',
                    });

                    if (choice.isDismissed) return;

                    url += '/approve';
                    method = 'POST';
                    body = JSON.stringify({
                        estado: choice.isConfirmed ? 'Aprobada' : 'Rechazada'
                    });
                } else if (action === 'assign' || action === 'reject') {
                    const choice = await Swal.fire({
                        title: action === 'assign' ? 'Asignar estudiante' : 'Rechazar postulación',
                        text: `${row.student} - ${row.offer}`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: action === 'assign' ? 'Confirmar asignación' : 'Rechazar',
                        cancelButtonText: 'Cancelar',
                    });

                    if (!choice.isConfirmed) return;

                    url += action === 'assign' ? '/assign' : '/reject';
                    method = 'POST';
                    body = '{}';
                } else if (action === 'transition') {
                    const choice = await Swal.fire({
                        title: `¿Cambiar estado a ${this.dataset.state}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Continuar',
                        cancelButtonText: 'Cancelar',
                    });

                    if (!choice.isConfirmed) return;

                    url += '/transition';
                    method = 'POST';
                    body = JSON.stringify({
                        estado: this.dataset.state
                    });
                } else {
                    const choice = await Swal.fire({
                        title: '¿Eliminar este registro?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar',
                    });

                    if (!choice.isConfirmed) return;
                }

                const response = await fetch(url, {
                    method,
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    ...(body ? {
                        body
                    } : {}),
                });

                const result = response.status === 204 ? {} : await response.json();

                if (!response.ok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo completar',
                        text: result.message ?? 'La operación falló.'
                    });
                    return;
                }

                table.ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: 'Actualizado',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        });
    </script>
@endpush
