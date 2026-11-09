@extends('layouts.app')

@section('title', 'Administración de usuarios')

@section('content')
    <div class="space-y-6">
        {{-- Encabezado principal --}}
        <div class="flex flex-col gap-4 border-b-2 border-slate-800/80 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">Panel de administración</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">Usuarios</h1>
                <p class="mt-1 text-sm text-slate-500">Consulta y administra las cuentas de la plataforma.</p>
            </div>

            <button type="button" onclick="openUserModal('create')"
                class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-slate-800 focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                <svg class="mr-2 h-5 w-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Agregar usuario
            </button>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <p class="font-semibold">No se pudo guardar el usuario. Revisa estos campos:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Contenedor de la tabla --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div
                class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 px-6 py-4 text-sm text-gray-600">
                <div>
                    <span>Usuarios registrados: <strong class="text-gray-900">{{ $users->count() }}</strong></span>
                    @if (isset($showInternalId) && !$showInternalId)
                        <span class="block text-xs text-gray-400">El identificador interno solo está disponible para Super Administrador.</span>
                    @endif
                </div>
                <div class="relative w-full md:max-w-sm">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="userSearch" placeholder="Buscar por nombre, RUT, correo o rol..."
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-[13px] text-slate-800 placeholder-slate-400 focus:bg-white focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100 transition">
                </div>
            </div>

            @if ($users->isEmpty())
                <div class="px-6 py-14 text-center text-sm text-gray-500">
                    No hay registros para mostrar.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table id="usersTable" class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50/50">
                            <tr>
                                @if (isset($showInternalId) && $showInternalId)
                                    <th scope="col"
                                        class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                        ID</th>
                                @endif
                                <th scope="col"
                                    class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    Nombre</th>
                                <th scope="col"
                                    class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    Apellidos</th>
                                <th scope="col"
                                    class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    RUT</th>
                                <th scope="col"
                                    class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    Género</th>
                                <th scope="col"
                                    class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    Edad</th>
                                <th scope="col"
                                    class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    Correo</th>
                                <th scope="col"
                                    class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    Rol</th>
                                <th scope="col"
                                    class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    @if (isset($showInternalId) && $showInternalId)
                                        <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-gray-500">
                                            {{ $user->id }}
                                        </td>
                                    @endif
                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                        {{ $user->last_name ?: '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-gray-600">
                                        {{ $user->rut ?: '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                        {{ $user->gender ?: '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-gray-600">
                                        {{ $user->age ?: '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                            {{ method_exists($user, 'getRoleNames') ? ($user->getRoleNames()->join(', ') ?: 'Sin rol') : 'Sin rol' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            {{-- Botón Editar (SVG de Lápiz Tailwind) --}}
                                            <button type="button" title="Editar usuario"
                                                data-user="{{ json_encode([
                                                    'id' => $user->id,
                                                    'name' => $user->name,
                                                    'last_name' => $user->last_name,
                                                    'rut' => $user->rut,
                                                    'gender' => $user->gender,
                                                    'age' => $user->age,
                                                    'email' => $user->email,
                                                    'role_id' => method_exists($user, 'roles') ? $user->roles->first()?->id : null,
                                                ]) }}"
                                                onclick="openUserModal('edit', JSON.parse(this.dataset.user))"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                                <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>

                                            {{-- Botón Eliminar (SVG de Papelera Tailwind) --}}
                                            @unless (auth()->user()?->is($user))
                                                <form id="delete-user-{{ $user->id }}"
                                                    action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="event.preventDefault(); confirmDelete('delete-user-{{ $user->id }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Eliminar usuario"
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 bg-white text-red-600 shadow-sm transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-100">
                                                        <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.8" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (method_exists($users, 'hasPages') && $users->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $users->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Modal Form -->
    <div id="userModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200">
        <div id="userModalPanel"
            class="max-h-[92vh] w-full max-w-2xl scale-95 overflow-y-auto rounded-xl bg-white shadow-2xl transition-transform duration-200">
            <div class="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
                <h2 id="userModalTitle" class="font-bold">Agregar usuario</h2>
                <button type="button" onclick="closeUserModal()" class="text-slate-300 hover:text-white"
                    aria-label="Cerrar">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <form id="userForm" action="{{ route('admin.users.store') }}" method="POST" class="space-y-4 p-6">
                @csrf
                <input type="hidden" name="_method" id="userFormMethod" value="POST">
                <input type="hidden" name="user_id" id="userId">

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="userName" class="mb-1 block text-xs font-semibold text-slate-700">Nombre *</label>
                        <input id="userName" name="name" required maxlength="255" autocomplete="given-name"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                    </div>
                    <div>
                        <label for="userLastName" class="mb-1 block text-xs font-semibold text-slate-700">Apellidos
                            *</label>
                        <input id="userLastName" name="last_name" required maxlength="255" autocomplete="family-name"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                    </div>
                    <div>
                        <label for="userRut" class="mb-1 block text-xs font-semibold text-slate-700">RUT *</label>
                        <input id="userRut" name="rut" required maxlength="12" placeholder="12.345.678-K"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                    </div>
                    <div>
                        <label for="userGender" class="mb-1 block text-xs font-semibold text-slate-700">Género *</label>
                        <select id="userGender" name="gender" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                            <option value="">Selecciona una opción</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Masculino">Masculino</option>
                            <option value="No binario">No binario</option>
                            <option value="Prefiero no decir">Prefiero no decir</option>
                        </select>
                    </div>
                    <div>
                        <label for="userAge" class="mb-1 block text-xs font-semibold text-slate-700">Edad *</label>
                        <input id="userAge" name="age" type="number" min="1" max="120" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                    </div>
                    <div>
                        <label for="userEmail" class="mb-1 block text-xs font-semibold text-slate-700">Correo electrónico
                            *</label>
                        <input id="userEmail" name="email" type="email" required maxlength="255"
                            autocomplete="email"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                    </div>
                    <div>
                        <label for="userRole" class="mb-1 block text-xs font-semibold text-slate-700">Rol *</label>
                        <select id="userRole" name="role_id" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                            <option value="">Selecciona un rol</option>
                            @if (isset($roles))
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="userPassword" class="mb-1 block text-xs font-semibold text-slate-700">
                            Contraseña <span id="passwordRequirement">*</span>
                        </label>
                        <input id="userPassword" name="password" type="password" minlength="8"
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                        <p id="passwordHelp" class="mt-1 text-[11px] text-slate-400">Debe tener al menos 8 caracteres.</p>
                    </div>
                    <div id="passwordConfirmationField">
                        <label for="userPasswordConfirmation" class="mb-1 block text-xs font-semibold text-slate-700">
                            Confirmar contraseña <span id="confirmationRequirement">*</span>
                        </label>
                        <input id="userPasswordConfirmation" name="password_confirmation" type="password" minlength="8"
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeUserModal()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancelar</button>
                    <button type="submit"
                        class="rounded-lg bg-[#C8102E] px-4 py-2 text-sm font-semibold text-white hover:bg-[#9e0b22]">Guardar
                        usuario</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const userModal = document.getElementById('userModal');
        const userModalPanel = document.getElementById('userModalPanel');
        const userForm = document.getElementById('userForm');
        const userFormMethod = document.getElementById('userFormMethod');
        const passwordInput = document.getElementById('userPassword');
        const passwordConfirmationInput = document.getElementById('userPasswordConfirmation');
        const oldUserValues = @js(old());
        const formHasErrors = @js($errors->any());
        const updateUserUrl = @js(route('admin.users.update', ['user' => '__USER__']));

        function openUserModal(mode, user = null) {
            userForm.reset();
            const isEditing = mode === 'edit';

            document.getElementById('userModalTitle').textContent = isEditing ? 'Editar usuario' : 'Agregar usuario';
            userForm.action = isEditing ? updateUserUrl.replace('__USER__', user.id ?? user.user_id) :
                @js(route('admin.users.store'));
            userFormMethod.value = isEditing ? 'PUT' : 'POST';
            document.getElementById('userId').value = isEditing ? (user.id ?? user.user_id ?? '') : '';

            document.getElementById('userName').value = user?.name ?? '';
            document.getElementById('userLastName').value = user?.last_name ?? '';
            document.getElementById('userRut').value = user?.rut ?? '';
            document.getElementById('userGender').value = user?.gender ?? '';
            document.getElementById('userAge').value = user?.age ?? '';
            document.getElementById('userEmail').value = user?.email ?? '';
            document.getElementById('userRole').value = user?.role_id ?? '';

            passwordInput.required = !isEditing;
            passwordConfirmationInput.required = !isEditing;
            document.getElementById('passwordRequirement').classList.toggle('hidden', isEditing);
            document.getElementById('confirmationRequirement').classList.toggle('hidden', isEditing);
            document.getElementById('passwordHelp').textContent = isEditing ?
                'Déjala vacía para conservar la contraseña actual.' :
                'Debe tener al menos 8 caracteres.';

            userModal.classList.remove('hidden');
            userModal.classList.add('flex');
            setTimeout(() => {
                userModal.classList.remove('opacity-0');
                userModalPanel.classList.remove('scale-95');
                userModalPanel.classList.add('scale-100');
            }, 10);
        }

        function closeUserModal() {
            userModalPanel.classList.remove('scale-100');
            userModalPanel.classList.add('scale-95');
            userModal.classList.add('opacity-0');
            setTimeout(() => {
                userModal.classList.add('hidden');
                userModal.classList.remove('flex');
            }, 200);
        }

        function confirmDelete(formId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se podrá revertir.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            } else {
                if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                    document.getElementById(formId).submit();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (formHasErrors) {
                openUserModal(oldUserValues.user_id ? 'edit' : 'create', oldUserValues);
            }
        });
    </script>
@endsection

@push('scripts')
    <script>
        // DataTables en el CRUD de usuarios (búsqueda, orden y paginación).
        if (document.getElementById('usersTable')) {
            initDataTable('#usersTable', '#userSearch', { order: [] });
        }
    </script>
@endpush
