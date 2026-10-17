@extends('layouts.app')

@section('title', 'Dashboard Administrador')

@section('content')
@role('Administrador')
    <div class="space-y-6">
        <!-- View Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b-2 border-slate-800/80">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="viewTitle">Carreras</h1>
            </div>

            <!-- Category Selector Dropdown -->
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <select id="sectionSelector" onchange="switchSection(this.value)"
                        class="appearance-none bg-transparent hover:bg-slate-200/50 text-slate-800 font-semibold text-sm py-1.5 pl-3 pr-8 rounded-lg border-b-2 border-slate-900 cursor-pointer focus:outline-none">
                        <option value="Carreras">Carreras</option>
                        <option value="Ofertas">Ofertas de Práctica</option>
                        <option value="Coordinadores">Coordinadores</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-600 pointer-events-none"></i>
                </div>
            </div>
        </div>

        <!-- Notification Alert Banner -->
        <div id="toastNotification"
            class="hidden transform transition-all duration-300 mb-4 p-4 rounded-xl border bg-white shadow-lg flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div id="toastIcon" class="w-8 h-8 rounded-full flex items-center justify-center text-white bg-green-500">
                    <i class="fa-solid fa-check text-sm"></i>
                </div>
                <div>
                    <p id="toastTitle" class="font-bold text-sm text-slate-800">Éxito</p>
                    <p id="toastMessage" class="text-xs text-slate-600">Operación realizada correctamente.</p>
                </div>
            </div>
            <button onclick="dismissToast()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- Main Card Wrapper -->
        <div class="bg-[#faf9f6] rounded-xl border border-slate-300/80 shadow-sm overflow-hidden p-4 md:p-6">

            <!-- Top Action Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                    <input type="search" id="searchInput" placeholder="Buscar..." oninput="onSearchInput(this.value)"
                        class="w-full sm:w-64 border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-slate-900 bg-white">
                    <select id="perPageSelect" onchange="changePerPage(this.value)"
                        class="border border-slate-300 rounded-lg py-2 px-2 text-xs bg-white outline-none">
                        <option value="5">5 por página</option>
                        <option value="10" selected>10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                    </select>
                </div>

                <!-- Add Record Button -->
                <button onclick="openModal('create')"
                    class="w-full sm:w-auto inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow transition-all hover:shadow-md focus:ring-2 focus:ring-slate-900 focus:ring-offset-1">
                    <i class="fa-solid fa-circle-plus mr-2 text-sm"></i>
                    <span>Agregar</span>
                </button>
            </div>

            <!-- Responsive Data Table -->
            <div class="overflow-x-auto rounded-lg border border-slate-200/80 bg-white">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr id="tableHeader" class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <!-- Inyectado dinámicamente -->
                        </tr>
                    </thead>
                    <tbody id="dataTableBody" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        <!-- Inyectado dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State View -->
            <div id="emptyState" class="hidden text-center py-12">
                <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-folder-open text-xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">No se encontraron registros</h3>
                <p class="text-xs text-slate-400 mt-1">Prueba cambiando el término de búsqueda o agrega un nuevo registro.</p>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-4">
                <div class="text-xs text-slate-500 font-medium" id="recordInfo">Cargando...</div>
                <div class="flex items-center space-x-2">
                    <button id="prevBtn" onclick="goToPage(state.page - 1)"
                        class="px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">Anterior</button>
                    <span class="text-xs text-slate-600 font-medium" id="pageInfo">1 / 1</span>
                    <button id="nextBtn" onclick="goToPage(state.page + 1)"
                        class="px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">Siguiente</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Dialog (Create & Edit) -->
    <div id="crudModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-200 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all scale-95 duration-200"
            id="modalContainer">

            <!-- Modal Header -->
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <h3 id="modalTitle" class="font-bold text-base flex items-center">
                    <i class="fa-solid fa-graduation-cap mr-2"></i> Agregar Registro
                </h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-white transition-colors focus:outline-none">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form (Contenido dinámico según la sección activa) -->
            <form id="activeForm" onsubmit="handleFormSubmit(event)" class="p-6 space-y-4">
                <input type="hidden" id="recordId">

                <div id="modalFieldsContainer">
                    <!-- Los inputs del formulario cambian según Carreras, Ofertas o Coordinadores -->
                </div>

                <div id="formError" class="hidden text-xs text-red-700 bg-red-50 border border-red-200 rounded-lg p-3"></div>

                <!-- Buttons Footer -->
                <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border border-slate-300 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 bg-[#C8102E] hover:bg-[#9e0b22] text-white text-xs font-semibold rounded-lg shadow transition-colors disabled:opacity-60">
                        Guardar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div id="deleteModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-200 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 text-center transform transition-all scale-95 duration-200"
            id="deleteModalContainer">
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-base">¿Eliminar registro?</h3>
            <p class="text-xs text-slate-500 mt-2">Esta acción no se puede deshacer. Se eliminará de la base de datos.</p>

            <div class="mt-6 flex items-center justify-center space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 border border-slate-300 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                    Cancelar
                </button>
                <button id="confirmDeleteBtn" onclick="confirmDelete()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow transition-colors">
                    Sí, Eliminar
                </button>
            </div>
        </div>
    </div>

    <script>
        const CSRF = @json(csrf_token());

        const INPUT_CLASS = 'w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600';
        const LABEL_CLASS = 'block text-xs font-semibold text-slate-700 mb-1';

        const state = {
            section: 'Carreras',
            page: 1,
            perPage: 10,
            search: '',
            sort: null,
            dir: 'asc',
            rows: [],
            lastPage: 1,
        };
        let deleteTargetId = null;
        let careerOptions = [];
        let searchTimer = null;
        let requestSeq = 0;

        const sections = {
            Carreras: {
                url: '/admin/careers',
                singular: 'Carrera',
                columns: [
                    { key: 'id', label: 'ID', sortable: true, render: i => `<span class="font-mono font-semibold text-slate-600">${i.id}</span>` },
                    { key: 'nombre', label: 'Nombre', sortable: true, render: i => `<span class="font-semibold text-slate-800">${escapeHtml(i.nombre)}</span>` },
                    { key: 'codigo', label: 'Código', sortable: true, render: i => `<span class="font-mono text-slate-600 uppercase">${escapeHtml(i.codigo)}</span>` },
                    { key: 'supervisor', label: '% Supervisor', sortable: true, center: true, render: i => `<span class="font-mono">${Number(i.supervisor).toFixed(2)}</span>` },
                    { key: 'coordinador', label: '% Coordinador', sortable: true, center: true, render: i => `<span class="font-mono">${Number(i.coordinador).toFixed(2)}</span>` },
                    { key: 'activa', label: 'Activa', sortable: true, center: true, render: i => i.activa
                        ? '<span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full text-[10px] font-bold">Activa</span>'
                        : '<span class="text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full text-[10px]">Inactiva</span>' },
                ],
                fields: item => `
                    <div class="space-y-4">
                        <div>
                            <label class="${LABEL_CLASS}">Nombre de la Carrera *</label>
                            <input type="text" id="nombreInput" required value="${escapeHtml(item ? item.nombre : '')}" class="${INPUT_CLASS}">
                        </div>
                        <div>
                            <label class="${LABEL_CLASS}">Código Carrera *</label>
                            <input type="text" id="codigoInput" required value="${escapeHtml(item ? item.codigo : '')}" class="${INPUT_CLASS} uppercase">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="${LABEL_CLASS}">% Supervisor</label>
                                <input type="number" id="supervisorInput" step="0.01" min="0" max="100" required value="${item ? item.supervisor : 50}" class="${INPUT_CLASS}">
                            </div>
                            <div>
                                <label class="${LABEL_CLASS}">% Coordinador</label>
                                <input type="number" id="coordinadorInput" step="0.01" min="0" max="100" required value="${item ? item.coordinador : 50}" class="${INPUT_CLASS}">
                            </div>
                        </div>
                        <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                            <span class="text-xs font-semibold text-slate-700">Estado Activo</span>
                            <input type="checkbox" id="activaInput" ${!item || item.activa ? 'checked' : ''} class="w-4 h-4 accent-red-600">
                        </div>
                    </div>`,
                collect: () => ({
                    nombre: val('nombreInput'),
                    codigo: val('codigoInput'),
                    supervisor: parseFloat(val('supervisorInput')),
                    coordinador: parseFloat(val('coordinadorInput')),
                    activa: document.getElementById('activaInput').checked,
                }),
            },

            Ofertas: {
                url: '/admin/offers',
                singular: 'Oferta',
                columns: [
                    { key: 'id', label: 'ID', sortable: true, render: i => `<span class="font-mono font-semibold text-slate-600">${i.id}</span>` },
                    { key: 'titulo', label: 'Título de oferta', sortable: true, render: i => `<span class="font-semibold text-slate-800">${escapeHtml(i.titulo)}</span>` },
                    { key: 'empresa', label: 'Empresa', sortable: true, render: i => `<span class="text-slate-600">${escapeHtml(i.empresa)}</span>` },
                    { key: 'vacantes', label: 'Vacantes', sortable: true, center: true, render: i => `<span class="font-mono">${i.vacantes}</span>` },
                    { key: 'estado', label: 'Estado', sortable: true, center: true, render: i => `<span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold">${escapeHtml(i.estado)}</span>` },
                ],
                fields: item => `
                    <div class="space-y-4">
                        <div>
                            <label class="${LABEL_CLASS}">Título de la Oferta *</label>
                            <input type="text" id="tituloInput" required value="${escapeHtml(item ? item.titulo : '')}" class="${INPUT_CLASS}">
                        </div>
                        <div>
                            <label class="${LABEL_CLASS}">Empresa *</label>
                            <input type="text" id="empresaInput" required value="${escapeHtml(item ? item.empresa : '')}" class="${INPUT_CLASS}">
                        </div>
                        <div>
                            <label class="${LABEL_CLASS}">Vacantes *</label>
                            <input type="number" id="vacantesInput" min="1" required value="${item ? item.vacantes : 1}" class="${INPUT_CLASS}">
                        </div>
                        <div>
                            <label class="${LABEL_CLASS}">Estado</label>
                            <select id="estadoInput" class="${INPUT_CLASS}">
                                <option value="Disponible" ${item && item.estado === 'Disponible' ? 'selected' : ''}>Disponible</option>
                                <option value="Cerrada" ${item && item.estado === 'Cerrada' ? 'selected' : ''}>Cerrada</option>
                            </select>
                        </div>
                    </div>`,
                collect: () => ({
                    titulo: val('tituloInput'),
                    empresa: val('empresaInput'),
                    vacantes: parseInt(val('vacantesInput'), 10),
                    estado: val('estadoInput'),
                }),
            },

            Coordinadores: {
                url: '/admin/coordinators',
                singular: 'Coordinador',
                columns: [
                    { key: 'id', label: 'ID', sortable: true, render: i => `<span class="font-mono font-semibold text-slate-600">${i.id}</span>` },
                    { key: 'nombre', label: 'Nombre', sortable: true, render: i => `<span class="font-semibold text-slate-800">${escapeHtml(i.nombre)}</span>` },
                    { key: 'correo', label: 'Correo', sortable: true, render: i => `<span class="text-slate-600">${escapeHtml(i.correo)}</span>` },
                    { key: 'carrera', label: 'Carrera asignada', sortable: true, render: i => `<span class="text-slate-600">${escapeHtml(i.carrera ?? '—')}</span>` },
                    { key: 'telefono', label: 'Teléfono', sortable: true, render: i => `<span class="font-mono text-xs">${escapeHtml(i.telefono ?? '')}</span>` },
                ],
                fields: item => `
                    <div class="space-y-4">
                        <div>
                            <label class="${LABEL_CLASS}">Nombre Completo *</label>
                            <input type="text" id="nombreCoordInput" required value="${escapeHtml(item ? item.nombre : '')}" class="${INPUT_CLASS}">
                        </div>
                        <div>
                            <label class="${LABEL_CLASS}">Correo Electrónico *</label>
                            <input type="email" id="correoInput" required value="${escapeHtml(item ? item.correo : '')}" class="${INPUT_CLASS}">
                        </div>
                        <div>
                            <label class="${LABEL_CLASS}">Carrera Asignada *</label>
                            <select id="careerInput" required class="${INPUT_CLASS}">
                                <option value="">Selecciona una carrera</option>
                                ${careerOptions.map(c => `<option value="${c.id}" ${item && item.career_id === c.id ? 'selected' : ''}>${escapeHtml(c.nombre)}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="${LABEL_CLASS}">Teléfono</label>
                            <input type="text" id="telefonoInput" value="${escapeHtml(item ? (item.telefono ?? '') : '')}" class="${INPUT_CLASS}">
                        </div>
                    </div>`,
                collect: () => ({
                    nombre: val('nombreCoordInput'),
                    correo: val('correoInput'),
                    career_id: parseInt(val('careerInput'), 10) || null,
                    telefono: val('telefonoInput') || null,
                }),
                beforeOpen: async () => { careerOptions = await api('GET', '/admin/careers/options'); },
            },
        };

        const cfg = () => sections[state.section];

        document.addEventListener('DOMContentLoaded', loadData);

        function val(id) { return document.getElementById(id).value.trim(); }

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        async function api(method, url, body) {
            const res = await fetch(url, {
                method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: body !== undefined ? JSON.stringify(body) : undefined,
            });
            if (res.status === 204) return null;
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                const err = new Error(data.message || 'Ocurrió un error inesperado.');
                err.status = res.status;
                err.errors = data.errors || {};
                throw err;
            }
            return data;
        }

        async function loadData() {
            const mySeq = ++requestSeq;
            const params = new URLSearchParams({
                page: state.page,
                per_page: state.perPage,
                search: state.search,
            });
            if (state.sort) {
                params.set('sort', state.sort);
                params.set('dir', state.dir);
            }

            try {
                const res = await api('GET', `${cfg().url}?${params}`);
                if (mySeq !== requestSeq) return;

                if (res.data.length === 0 && res.meta.page > 1) {
                    state.page = res.meta.last_page || 1;
                    return loadData();
                }

                state.rows = res.data;
                state.lastPage = res.meta.last_page;
                renderTable(res.meta);
            } catch (e) {
                showToast('Error', e.message, 'error');
            }
        }

        function renderTable(meta) {
            const { columns } = cfg();
            const thead = document.getElementById('tableHeader');
            const tbody = document.getElementById('dataTableBody');
            const emptyState = document.getElementById('emptyState');

            thead.innerHTML = columns.map(col => {
                const align = col.center ? 'text-center' : '';
                if (!col.sortable) return `<th class="py-3 px-4 ${align}">${col.label}</th>`;
                const arrow = state.sort === col.key ? (state.dir === 'asc' ? ' ▲' : ' ▼') : '';
                return `<th class="py-3 px-4 ${align} cursor-pointer select-none hover:text-slate-800" onclick="sortBy('${col.key}')">${col.label}${arrow}</th>`;
            }).join('') + '<th class="py-3 px-4 text-center">Acciones</th>';

            tbody.innerHTML = state.rows.map(item => `
                <tr class="hover:bg-slate-50/80 transition-colors border-b border-slate-100">
                    ${columns.map(col => `<td class="py-3 px-4 ${col.center ? 'text-center' : ''}">${col.render(item)}</td>`).join('')}
                    <td class="py-3 px-4 text-center">
                        <button onclick="openModal('edit', ${item.id})" class="px-2.5 py-1 text-xs border border-slate-400 text-slate-700 bg-white font-semibold rounded hover:bg-slate-100">Editar</button>
                        <button onclick="openDeleteModal(${item.id})" class="px-2.5 py-1 text-xs border border-red-300 text-red-600 bg-white font-semibold rounded hover:bg-red-50 ml-1">Eliminar</button>
                    </td>
                </tr>`).join('');

            emptyState.classList.toggle('hidden', state.rows.length > 0);

            document.getElementById('recordInfo').innerHTML = meta.filtered === 0
                ? 'Mostrando <span class="font-bold text-slate-800">0</span> registros'
                : `Mostrando <span class="font-bold text-slate-800">${meta.from}–${meta.to}</span> de <span class="font-bold text-slate-800">${meta.filtered}</span> registros`
                  + (meta.filtered !== meta.total ? ` (filtrados de ${meta.total})` : '');

            document.getElementById('pageInfo').textContent = `${meta.page} / ${meta.last_page}`;
            document.getElementById('prevBtn').disabled = meta.page <= 1;
            document.getElementById('nextBtn').disabled = meta.page >= meta.last_page;
        }

        function switchSection(section) {
            state.section = section;
            state.page = 1;
            state.sort = null;
            state.dir = 'asc';
            state.search = '';
            document.getElementById('searchInput').value = '';
            document.getElementById('viewTitle').textContent = section;
            loadData();
        }

        function onSearchInput(value) {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                state.search = value.trim();
                state.page = 1;
                loadData();
            }, 300);
        }

        function changePerPage(value) {
            state.perPage = parseInt(value, 10);
            state.page = 1;
            loadData();
        }

        function sortBy(key) {
            if (state.sort === key) {
                state.dir = state.dir === 'asc' ? 'desc' : 'asc';
            } else {
                state.sort = key;
                state.dir = 'asc';
            }
            state.page = 1;
            loadData();
        }

        function goToPage(page) {
            if (page < 1 || page > state.lastPage) return;
            state.page = page;
            loadData();
        }

        async function openModal(mode, id = null) {
            const c = cfg();
            try {
                if (c.beforeOpen) await c.beforeOpen();
            } catch (e) {
                showToast('Error', e.message, 'error');
                return;
            }

            const modal = document.getElementById('crudModal');
            const modalContainer = document.getElementById('modalContainer');

            document.getElementById('modalTitle').innerHTML =
                `<i class="fa-solid ${mode === 'create' ? 'fa-plus-circle' : 'fa-pen-to-square'} mr-2"></i> ${mode === 'create' ? 'Agregar' : 'Editar'} ${c.singular}`;
            document.getElementById('recordId').value = id !== null ? id : '';
            hideFormError();

            const item = mode === 'edit' ? state.rows.find(r => r.id === id) : null;
            document.getElementById('modalFieldsContainer').innerHTML = c.fields(item);

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContainer.classList.remove('scale-95');
                modalContainer.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('crudModal');
            const modalContainer = document.getElementById('modalContainer');
            modalContainer.classList.remove('scale-100');
            modalContainer.classList.add('scale-95');
            modal.classList.add('opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        function showFormError(err) {
            const box = document.getElementById('formError');
            const messages = Object.values(err.errors || {}).flat();
            box.innerHTML = (messages.length ? messages : [err.message]).map(escapeHtml).join('<br>');
            box.classList.remove('hidden');
        }

        function hideFormError() {
            document.getElementById('formError').classList.add('hidden');
        }

        async function handleFormSubmit(e) {
            e.preventDefault();
            hideFormError();

            const id = document.getElementById('recordId').value;
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;

            try {
                const payload = cfg().collect();
                if (id) {
                    await api('PUT', `${cfg().url}/${id}`, payload);
                } else {
                    await api('POST', cfg().url, payload);
                    state.page = 1;
                }
                closeModal();
                await loadData();
                showToast('¡Éxito!', 'Registro guardado correctamente.', 'success');
            } catch (err) {
                showFormError(err);
            } finally {
                btn.disabled = false;
            }
        }

        function openDeleteModal(id) {
            deleteTargetId = id;
            const modal = document.getElementById('deleteModal');
            const modalContainer = document.getElementById('deleteModalContainer');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContainer.classList.remove('scale-95');
                modalContainer.classList.add('scale-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const modalContainer = document.getElementById('deleteModalContainer');
            modalContainer.classList.remove('scale-100');
            modalContainer.classList.add('scale-95');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                deleteTargetId = null;
            }, 200);
        }

        async function confirmDelete() {
            if (deleteTargetId === null) return;
            const id = deleteTargetId;
            closeDeleteModal();
            try {
                await api('DELETE', `${cfg().url}/${id}`);
                await loadData();
                showToast('Eliminado', 'El registro ha sido removido.', 'info');
            } catch (err) {
                showToast('Error', err.message, 'error');
            }
        }

        function showToast(title, message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastMessage').textContent = message;
            const toastIcon = document.getElementById('toastIcon');
            const styles = {
                success: ['bg-emerald-500', 'fa-check'],
                info: ['bg-blue-500', 'fa-info'],
                error: ['bg-red-500', 'fa-xmark'],
            };
            const [bg, icon] = styles[type] || styles.info;
            toastIcon.className = `w-8 h-8 rounded-full flex items-center justify-center text-white ${bg}`;
            toastIcon.innerHTML = `<i class="fa-solid ${icon} text-sm"></i>`;

            toast.classList.remove('hidden');
            clearTimeout(showToast.timer);
            showToast.timer = setTimeout(dismissToast, 3500);
        }

        function dismissToast() {
            document.getElementById('toastNotification').classList.add('hidden');
        }
    </script>
@endrole
@endsection
