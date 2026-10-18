@extends('layouts.app')

@section('title', $config['title'])

@section('content')
    <section class="space-y-6">
        <header class="flex flex-col gap-4 border-b border-slate-300 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase text-red-700">Gestión institucional</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $config['title'] }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $config['description'] }}</p>
            </div>
            @if ($config['canCreate'] && count($config['fields']))
                <button type="button" id="createRecord"
                    class="rounded-md bg-red-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2">
                    <i class="fa-solid fa-plus mr-2" aria-hidden="true"></i>Nuevo registro
                </button>
            @endif
        </header>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto p-3 sm:p-5">
                <table id="resourceTable" class="w-full min-w-[760px] whitespace-nowrap text-left text-sm text-slate-700">
                    <thead>
                        <tr>
                            @foreach ($config['columns'] as $column)
                                <th>{{ $column['title'] }}</th>
                            @endforeach
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>

    @if ($config['canCreate'] && count($config['fields']))
        <div id="recordModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-3 sm:p-6"
            role="dialog" aria-modal="true" aria-labelledby="recordModalTitle">
            <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 id="recordModalTitle" class="text-lg font-bold text-slate-900">Nuevo registro</h2>
                    <button type="button" id="closeRecordModal" class="rounded p-2 text-slate-500 hover:bg-slate-100" aria-label="Cerrar">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>
                <form id="recordForm" class="space-y-5 p-5 sm:p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($config['fields'] as $field)
                            <div class="{{ $field['type'] === 'textarea' ? 'sm:col-span-2' : '' }}">
                                <label for="field-{{ $field['name'] }}" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    {{ $field['label'] }}
                                </label>
                                @if ($field['type'] === 'select')
                                    <select id="field-{{ $field['name'] }}" name="{{ $field['name'] }}"
                                        @if ($field['multiple'] ?? false) multiple size="4" @endif
                                        class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                                        @unless ($field['multiple'] ?? false)
                                            <option value="">Selecciona una opción</option>
                                        @endunless
                                        @foreach ($field['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field['type'] === 'textarea')
                                    <textarea id="field-{{ $field['name'] }}" name="{{ $field['name'] }}" rows="3"
                                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100"></textarea>
                                @elseif ($field['type'] === 'checkbox')
                                    <input id="field-{{ $field['name'] }}" name="{{ $field['name'] }}" type="checkbox" value="1"
                                        class="h-4 w-4 rounded border-slate-300 text-red-700 focus:ring-red-600">
                                @else
                                    <input id="field-{{ $field['name'] }}" name="{{ $field['name'] }}" type="{{ $field['type'] }}"
                                        @foreach ($field['attributes'] ?? [] as $attribute => $value) {{ $attribute }}="{{ $value }}" @endforeach
                                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <p id="recordFormError" class="hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800" role="alert"></p>
                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" id="cancelRecord" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</button>
                        <button type="submit" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Guardar</button>
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
            const rowCache = new Map();
            const columnNames = config.columns.map((column) => column.data);

            const table = $('#resourceTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                ajax: async (request, callback) => {
                    const order = request.order?.[0] ?? { column: 0, dir: 'asc' };
                    const params = new URLSearchParams({
                        page: String(Math.floor(request.start / request.length) + 1),
                        per_page: String(request.length),
                        search: request.search.value,
                        sort: columnNames[order.column] ?? 'id',
                        dir: order.dir,
                    });

                    try {
                        const response = await fetch(`${config.dataUrl ?? config.apiUrl}?${params}`, { headers: { Accept: 'application/json' } });
                        const result = await response.json();
                        if (!response.ok) throw new Error(result.message ?? 'No se pudieron cargar los registros.');
                        result.data.forEach((row) => rowCache.set(String(row.id), row));
                        callback({
                            draw: request.draw,
                            recordsTotal: result.meta.total,
                            recordsFiltered: result.meta.filtered,
                            data: result.data,
                        });
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Error de carga', text: error.message });
                        callback({ draw: request.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                    }
                },
                columns: [
                    ...config.columns.map((column) => ({
                        ...column,
                        render: (value, type) => {
                            if (column.data === 'activa') {
                                const label = value ? 'Activa' : 'Inactiva';
                                return type === 'display' ? label : value;
                            }
                            const text = Array.isArray(value) ? value.join(', ') : (value ?? '');
                            return type === 'display' ? $.fn.dataTable.render.text().display(String(text)) : text;
                        },
                    })),
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (_value, _type, row) => {
                            const actions = config.fields.length ? ['<button type="button" data-action="edit" class="mr-2 text-red-700 hover:underline">Editar</button>'] : [];
                            if (config.actions.includes('approval') && row.estado === 'Pendiente') actions.push('<button type="button" data-action="approval" class="mr-2 text-emerald-700 hover:underline">Revisar</button>');
                            if (config.actions.includes('application') && row.estado === 'Postulado') {
                                actions.push('<button type="button" data-action="assign" class="mr-2 text-emerald-700 hover:underline">Asignar</button>');
                                actions.push('<button type="button" data-action="reject" class="mr-2 text-amber-700 hover:underline">Rechazar</button>');
                            }
                            if (config.actions.includes('transition')) {
                                const next = { Asignado: 'En Curso', 'En Curso': 'Informe Entregado', 'Informe Entregado': 'Finalizada' }[row.estado];
                                if (next) actions.push(`<button type="button" data-action="transition" data-state="${next}" class="mr-2 text-emerald-700 hover:underline">${next}</button>`);
                            }
                            if (config.canDelete !== false) actions.push('<button type="button" data-action="delete" class="text-red-700 hover:underline">Eliminar</button>');
                            return `<div class="whitespace-nowrap">${actions.join('')}</div>`;
                        },
                    },
                ],
            });

            const modal = document.getElementById('recordModal');
            const form = document.getElementById('recordForm');
            let editingId = null;

            const showModal = (row = null) => {
                if (!modal || !form) return;
                editingId = row?.id ?? null;
                form.reset();
                document.getElementById('recordModalTitle').textContent = editingId ? 'Editar registro' : 'Nuevo registro';
                document.getElementById('recordFormError').classList.add('hidden');
                config.fields.forEach((field) => {
                    const input = form.elements.namedItem(field.name);
                    if (!input) return;
                    let value = row?.[field.name] ?? '';
                    if (field.type === 'tags' && Array.isArray(value)) value = value.join(', ');
                    if (input instanceof HTMLSelectElement && input.multiple) {
                        [...input.options].forEach((option) => { option.selected = (row?.[field.name] ?? []).map(String).includes(option.value); });
                    } else if (field.type === 'checkbox') {
                        input.checked = Boolean(value);
                    } else {
                        input.value = value;
                    }
                });
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const hideModal = () => {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                editingId = null;
            };

            document.getElementById('createRecord')?.addEventListener('click', () => showModal());
            document.getElementById('closeRecordModal')?.addEventListener('click', hideModal);
            document.getElementById('cancelRecord')?.addEventListener('click', hideModal);

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                const payload = {};
                config.fields.forEach((field) => {
                    const input = form.elements.namedItem(field.name);
                    if (field.type === 'checkbox') payload[field.name] = input.checked;
                    else if (input instanceof HTMLSelectElement && input.multiple) payload[field.name] = [...input.selectedOptions].map((option) => option.value);
                    else if (field.type === 'tags') payload[field.name] = input.value.split(',').map((value) => value.trim()).filter(Boolean);
                    else payload[field.name] = input.value;
                });
                Object.assign(payload, config.defaults ?? {});
                const response = await fetch(editingId ? `${config.apiUrl}/${editingId}` : config.apiUrl, {
                    method: editingId ? 'PUT' : 'POST',
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(payload),
                });
                const result = response.status === 204 ? {} : await response.json();
                if (!response.ok) {
                    const message = Object.values(result.errors ?? {}).flat().join(' ') || result.message || 'No se pudo guardar el registro.';
                    const error = document.getElementById('recordFormError');
                    error.textContent = message;
                    error.classList.remove('hidden');
                    return;
                }
                hideModal();
                table.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Guardado', text: 'El registro se guardó correctamente.', timer: 1800, showConfirmButton: false });
            });

            $('#resourceTable').on('click', '[data-action]', async function () {
                const row = table.row($(this).closest('tr')).data();
                if (!row) return;
                const id = row.id;
                const action = this.dataset.action;
                if (action === 'edit') {
                    if (config.fields.length) showModal(row);
                    return;
                }
                let url = `${config.apiUrl}/${id}`;
                let method = 'DELETE';
                let body;
                if (action === 'approval') {
                    const choice = await Swal.fire({
                        title: 'Revisar oferta', text: '¿Apruebas esta oferta para publicarla?', icon: 'question',
                        showCancelButton: true, showDenyButton: true, confirmButtonText: 'Aprobar', denyButtonText: 'Rechazar', cancelButtonText: 'Cancelar',
                    });
                    if (choice.isDismissed) return;
                    url += '/approve'; method = 'POST'; body = JSON.stringify({ estado: choice.isConfirmed ? 'Aprobada' : 'Rechazada' });
                } else if (action === 'assign' || action === 'reject') {
                    const choice = await Swal.fire({
                        title: action === 'assign' ? 'Asignar estudiante' : 'Rechazar postulación',
                        text: `${row.student} - ${row.offer}`, icon: 'question', showCancelButton: true,
                        confirmButtonText: action === 'assign' ? 'Confirmar asignación' : 'Rechazar', cancelButtonText: 'Cancelar',
                    });
                    if (!choice.isConfirmed) return;
                    url += action === 'assign' ? '/assign' : '/reject'; method = 'POST'; body = '{}';
                } else if (action === 'transition') {
                    const choice = await Swal.fire({ title: `Cambiar estado a ${this.dataset.state}?`, icon: 'question', showCancelButton: true, confirmButtonText: 'Continuar', cancelButtonText: 'Cancelar' });
                    if (!choice.isConfirmed) return;
                    url += '/transition'; method = 'POST'; body = JSON.stringify({ estado: this.dataset.state });
                } else {
                    const choice = await Swal.fire({ title: '¿Eliminar este registro?', text: 'Esta acción no se puede deshacer.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar' });
                    if (!choice.isConfirmed) return;
                }

                const response = await fetch(url, {
                    method,
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    ...(body ? { body } : {}),
                });
                const result = response.status === 204 ? {} : await response.json();
                if (!response.ok) {
                    Swal.fire({ icon: 'error', title: 'No se pudo completar', text: result.message ?? 'La operación falló.' });
                    return;
                }
                table.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Actualizado', timer: 1500, showConfirmButton: false });
            });
        });
    </script>
@endpush
<div>
    <!-- Simplicity is an acquired taste. - Katharine Gerould -->
</div>
