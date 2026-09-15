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
                <div class="text-xs text-slate-500 font-medium">
                    Mostrando <span id="recordCount" class="font-bold text-slate-800">0</span> registros
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

                <!-- Buttons Footer -->
                <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border border-slate-300 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#C8102E] hover:bg-[#9e0b22] text-white text-xs font-semibold rounded-lg shadow transition-colors">
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
        // Fuentes de datos para cada sección
        let currentSection = 'Carreras';
        let deleteTargetId = null;

        let datasets = {
            'Carreras': [
                { id: 3, nombre: 'Ingeniería Comercial', codigo: 'ICOM', supervisor: 55.00, coordinador: 45.00, activa: true },
                { id: 2, nombre: 'Ingeniería Civil Industrial', codigo: 'ICI2', supervisor: 50.00, coordinador: 50.00, activa: true },
                { id: 1, nombre: 'Ingeniería Civil Informática', codigo: 'ICI', supervisor: 60.00, coordinador: 40.00, activa: true }
            ],
            'Ofertas': [
                { id: 1, titulo: 'Desarrollador Backend Laravel', empresa: 'Tech Solutions SpA', vacantes: 3, estado: 'Disponible' },
                { id: 2, titulo: 'Analista de Datos Jr', empresa: 'Banco de Concepción', vacantes: 1, estado: 'Disponible' }
            ],
            'Coordinadores': [
                { id: 1, nombre: 'Roberto Soto', correo: 'rsoto@ucsc.cl', carrera: 'Ingeniería Civil Informática', telefono: '+56912345678' },
                { id: 2, nombre: 'Claudia Valenzuela', correo: 'cvalenzuela@ucsc.cl', carrera: 'Ingeniería Comercial', telefono: '+56987654321' }
            ]
        };

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });

        function switchSection(section) {
            currentSection = section;
            document.getElementById('viewTitle').textContent = section;
            renderTable();
            showToast('Vista Cambiada', `Mostrando listado de ${section}.`, 'info');
        }

        function renderTable() {
            const thead = document.getElementById('tableHeader');
            const tbody = document.getElementById('dataTableBody');
            const emptyState = document.getElementById('emptyState');
            const recordCount = document.getElementById('recordCount');
            const data = datasets[currentSection];

            recordCount.textContent = data.length;

            if (data.length === 0) {
                thead.innerHTML = '';
                tbody.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');

            if (currentSection === 'Carreras') {
                thead.innerHTML = `
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">NOMBRE</th>
                    <th class="py-3 px-4">CÓDIGO</th>
                    <th class="py-3 px-4 text-center">% SUPERVISOR</th>
                    <th class="py-3 px-4 text-center">% COORDINADOR</th>
                    <th class="py-3 px-4 text-center">ACTIVA</th>
                    <th class="py-3 px-4 text-center">ACCIONES</th>
                `;
                tbody.innerHTML = data.map(item => `
                    <tr class="hover:bg-slate-50/80 transition-colors border-b border-slate-100">
                        <td class="py-3 px-4 font-mono font-semibold text-slate-600">${item.id}</td>
                        <td class="py-3 px-4 font-semibold text-slate-800">${escapeHtml(item.nombre)}</td>
                        <td class="py-3 px-4 font-mono text-slate-600 uppercase">${escapeHtml(item.codigo)}</td>
                        <td class="py-3 px-4 text-center font-mono">${item.supervisor.toFixed(2)}</td>
                        <td class="py-3 px-4 text-center font-mono">${item.coordinador.toFixed(2)}</td>
                        <td class="py-3 px-4 text-center">
                            ${item.activa ? '<span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full text-[10px] font-bold">Activa</span>' : '<span class="text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full text-[10px]">Inactiva</span>'}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button onclick="openModal('edit', ${item.id})" class="px-2.5 py-1 text-xs border border-slate-400 text-slate-700 bg-white font-semibold rounded hover:bg-slate-100">Editar</button>
                            <button onclick="openDeleteModal(${item.id})" class="px-2.5 py-1 text-xs border border-red-300 text-red-600 bg-white font-semibold rounded hover:bg-red-50 ml-1">Eliminar</button>
                        </td>
                    </tr>
                `).join('');
            } else if (currentSection === 'Ofertas') {
                thead.innerHTML = `
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">TÍTULO DE OFERTA</th>
                    <th class="py-3 px-4">EMPRESA</th>
                    <th class="py-3 px-4 text-center">VACANTES</th>
                    <th class="py-3 px-4 text-center">ESTADO</th>
                    <th class="py-3 px-4 text-center">ACCIONES</th>
                `;
                tbody.innerHTML = data.map(item => `
                    <tr class="hover:bg-slate-50/80 transition-colors border-b border-slate-100">
                        <td class="py-3 px-4 font-mono font-semibold text-slate-600">${item.id}</td>
                        <td class="py-3 px-4 font-semibold text-slate-800">${escapeHtml(item.titulo)}</td>
                        <td class="py-3 px-4 text-slate-600">${escapeHtml(item.empresa)}</td>
                        <td class="py-3 px-4 text-center font-mono">${item.vacantes}</td>
                        <td class="py-3 px-4 text-center"><span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold">${item.estado}</span></td>
                        <td class="py-3 px-4 text-center">
                            <button onclick="openModal('edit', ${item.id})" class="px-2.5 py-1 text-xs border border-slate-400 text-slate-700 bg-white font-semibold rounded hover:bg-slate-100">Editar</button>
                            <button onclick="openDeleteModal(${item.id})" class="px-2.5 py-1 text-xs border border-red-300 text-red-600 bg-white font-semibold rounded hover:bg-red-50 ml-1">Eliminar</button>
                        </td>
                    </tr>
                `).join('');
            } else if (currentSection === 'Coordinadores') {
                thead.innerHTML = `
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">NOMBRE</th>
                    <th class="py-3 px-4">CORREO</th>
                    <th class="py-3 px-4">CARRERA ASIGNADA</th>
                    <th class="py-3 px-4">TELÉFONO</th>
                    <th class="py-3 px-4 text-center">ACCIONES</th>
                `;
                tbody.innerHTML = data.map(item => `
                    <tr class="hover:bg-slate-50/80 transition-colors border-b border-slate-100">
                        <td class="py-3 px-4 font-mono font-semibold text-slate-600">${item.id}</td>
                        <td class="py-3 px-4 font-semibold text-slate-800">${escapeHtml(item.nombre)}</td>
                        <td class="py-3 px-4 text-slate-600">${escapeHtml(item.correo)}</td>
                        <td class="py-3 px-4 text-slate-600">${escapeHtml(item.carrera)}</td>
                        <td class="py-3 px-4 font-mono text-xs">${escapeHtml(item.telefono)}</td>
                        <td class="py-3 px-4 text-center">
                            <button onclick="openModal('edit', ${item.id})" class="px-2.5 py-1 text-xs border border-slate-400 text-slate-700 bg-white font-semibold rounded hover:bg-slate-100">Editar</button>
                            <button onclick="openDeleteModal(${item.id})" class="px-2.5 py-1 text-xs border border-red-300 text-red-600 bg-white font-semibold rounded hover:bg-red-50 ml-1">Eliminar</button>
                        </td>
                    </tr>
                `).join('');
            }
        }

        function openModal(mode, id = null) {
            const modal = document.getElementById('crudModal');
            const modalContainer = document.getElementById('modalContainer');
            const modalTitle = document.getElementById('modalTitle');
            const fieldsContainer = document.getElementById('modalFieldsContainer');

            modalTitle.innerHTML = `<i class="fa-solid ${mode === 'create' ? 'fa-plus-circle' : 'fa-pen-to-square'} mr-2"></i> ${mode === 'create' ? 'Agregar ' + currentSection.slice(0, -1) : 'Editar ' + currentSection.slice(0, -1)}`;
            document.getElementById('recordId').value = id !== null ? id : '';

            let item = mode === 'edit' ? datasets[currentSection].find(c => c.id === id) : null;

            if (currentSection === 'Carreras') {
                fieldsContainer.innerHTML = `
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre de la Carrera *</label>
                        <input type="text" id="nombreInput" required value="${item ? item.nombre : ''}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Código Carrera *</label>
                        <input type="text" id="codigoInput" required value="${item ? item.codigo : ''}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs uppercase outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">% Supervisor</label>
                            <input type="number" id="supervisorInput" step="0.01" min="0" max="100" required value="${item ? item.supervisor : 50}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">% Coordinador</label>
                            <input type="number" id="coordinadorInput" step="0.01" min="0" max="100" required value="${item ? item.coordinador : 50}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs">
                        </div>
                    </div>
                    <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                        <span class="text-xs font-semibold text-slate-700">Estado Activo</span>
                        <input type="checkbox" id="activaInput" ${!item || item.activa ? 'checked' : ''} class="w-4 h-4 accent-red-600">
                    </div>
                `;
            } else if (currentSection === 'Ofertas') {
                fieldsContainer.innerHTML = `
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Título de la Oferta *</label>
                        <input type="text" id="tituloInput" required value="${item ? item.titulo : ''}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Empresa *</label>
                        <input type="text" id="empresaInput" required value="${item ? item.empresa : ''}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Vacantes *</label>
                        <input type="number" id="vacantesInput" min="1" required value="${item ? item.vacantes : 1}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Estado</label>
                        <select id="estadoInput" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none">
                            <option value="Disponible" ${item && item.estado === 'Disponible' ? 'selected' : ''}>Disponible</option>
                            <option value="Cerrada" ${item && item.estado === 'Cerrada' ? 'selected' : ''}>Cerrada</option>
                        </select>
                    </div>
                `;
            } else if (currentSection === 'Coordinadores') {
                fieldsContainer.innerHTML = `
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Completo *</label>
                        <input type="text" id="nombreCoordInput" required value="${item ? item.nombre : ''}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico *</label>
                        <input type="email" id="correoInput" required value="${item ? item.correo : ''}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Carrera Asignada *</label>
                        <input type="text" id="carreraCoordInput" required value="${item ? item.carrera : ''}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                        <input type="text" id="telefonoInput" value="${item ? item.telefono : ''}" class="w-full border border-slate-300 rounded-lg py-2 px-3 text-xs outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                `;
            }

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

        function handleFormSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('recordId').value;
            let dataList = datasets[currentSection];

            if (currentSection === 'Carreras') {
                const nombre = document.getElementById('nombreInput').value.trim();
                const codigo = document.getElementById('codigoInput').value.trim();
                const supervisor = parseFloat(document.getElementById('supervisorInput').value) || 0;
                const coordinador = parseFloat(document.getElementById('coordinadorInput').value) || 0;
                const activa = document.getElementById('activaInput').checked;

                if (id) {
                    let index = dataList.findIndex(c => c.id === parseInt(id));
                    dataList[index] = { id: parseInt(id), nombre, codigo, supervisor, coordinador, activa };
                } else {
                    const newId = dataList.length > 0 ? Math.max(...dataList.map(c => c.id)) + 1 : 1;
                    dataList.unshift({ id: newId, nombre, codigo, supervisor, coordinador, activa });
                }
            } else if (currentSection === 'Ofertas') {
                const titulo = document.getElementById('tituloInput').value.trim();
                const empresa = document.getElementById('empresaInput').value.trim();
                const vacantes = parseInt(document.getElementById('vacantesInput').value) || 1;
                const estado = document.getElementById('estadoInput').value;

                if (id) {
                    let index = dataList.findIndex(c => c.id === parseInt(id));
                    dataList[index] = { id: parseInt(id), titulo, empresa, vacantes, estado };
                } else {
                    const newId = dataList.length > 0 ? Math.max(...dataList.map(c => c.id)) + 1 : 1;
                    dataList.unshift({ id: newId, titulo, empresa, vacantes, estado });
                }
            } else if (currentSection === 'Coordinadores') {
                const nombre = document.getElementById('nombreCoordInput').value.trim();
                const correo = document.getElementById('correoInput').value.trim();
                const carrera = document.getElementById('carreraCoordInput').value.trim();
                const telefono = document.getElementById('telefonoInput').value.trim();

                if (id) {
                    let index = dataList.findIndex(c => c.id === parseInt(id));
                    dataList[index] = { id: parseInt(id), nombre, correo, carrera, telefono };
                } else {
                    const newId = dataList.length > 0 ? Math.max(...dataList.map(c => c.id)) + 1 : 1;
                    dataList.unshift({ id: newId, nombre, correo, carrera, telefono });
                }
            }

            closeModal();
            renderTable();
            showToast('¡Éxito!', 'Registro guardado correctamente.', 'success');
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

        function confirmDelete() {
            if (deleteTargetId !== null) {
                datasets[currentSection] = datasets[currentSection].filter(c => c.id !== deleteTargetId);
                renderTable();
                showToast('Eliminado', 'El registro ha sido removido.', 'info');
            }
            closeDeleteModal();
        }

        function showToast(title, message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastMessage').textContent = message;
            const toastIcon = document.getElementById('toastIcon');

            if (type === 'success') {
                toastIcon.className = 'w-8 h-8 rounded-full flex items-center justify-center text-white bg-emerald-500';
                toastIcon.innerHTML = '<i class="fa-solid fa-check text-sm"></i>';
            } else {
                toastIcon.className = 'w-8 h-8 rounded-full flex items-center justify-center text-white bg-blue-500';
                toastIcon.innerHTML = '<i class="fa-solid fa-info text-sm"></i>';
            }

            toast.classList.remove('hidden');
            setTimeout(() => dismissToast(), 3500);
        }

        function dismissToast() {
            document.getElementById('toastNotification').classList.add('hidden');
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    </script>
@endrole
@endsection
