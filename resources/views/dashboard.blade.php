@extends('layouts.app')

@section('title', 'Administración de usuarios')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 border-b-2 border-slate-800/80 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">Panel de administración</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">Usuarios</h1>
                <p class="mt-1 text-sm text-slate-500">Consulta y administra las cuentas de la plataforma.</p>
            </div>

            <button type="button" onclick="openUserModal('create')"
                class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-slate-800 focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                <i class="fa-solid fa-circle-plus mr-2"></i>
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

        <section class="overflow-hidden rounded-xl border border-slate-300/80 bg-[#faf9f6] p-4 shadow-sm md:p-6">
            <div class="mb-5 flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">
                    Usuarios registrados:
                    <span class="font-bold text-slate-800">{{ $users->count() }}</span>
                </p>
                @if (!$showInternalId)
                    <p class="text-[11px] text-slate-400">El identificador interno solo está disponible para Super
                        Administrador.</p>
                @endif
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200/80 bg-white">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr
                            class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            @if ($showInternalId)
                                <th class="px-3 py-3">ID interno</th>
                            @endif
                            <th class="px-3 py-3">Nombre</th>
                            <th class="px-3 py-3">Apellidos</th>
                            <th class="px-3 py-3">RUT</th>
                            <th class="px-3 py-3">Género</th>
                            <th class="px-3 py-3 text-center">Edad</th>
                            <th class="px-3 py-3">Correo</th>
                            <th class="px-3 py-3">Rol</th>
                            <th class="px-3 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @forelse ($users as $user)
                            <tr class="transition-colors hover:bg-slate-50">
                                @if ($showInternalId)
                                    <td class="whitespace-nowrap px-3 py-3 font-mono font-semibold text-slate-500">
                                        {{ $user->id }}</td>
                                @endif
                                <td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-800">{{ $user->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $user->last_name ?: '—' }}</td>
                                <td class="whitespace-nowrap px-3 py-3 font-mono">{{ $user->rut ?: '—' }}</td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $user->gender ?: '—' }}</td>
                                <td class="px-3 py-3 text-center">{{ $user->age ?: '—' }}</td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-3 py-3">
                                    {{ $user->getRoleNames()->join(', ') ?: 'Sin rol' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center">
                                    <button type="button"
                                        data-user="{{ json_encode([
                                            'id' => $user->id,
                                            'name' => $user->name,
                                            'last_name' => $user->last_name,
                                            'rut' => $user->rut,
                                            'gender' => $user->gender,
                                            'age' => $user->age,
                                            'email' => $user->email,
                                            'role_id' => $user->roles->first()?->id,
                                        ]) }}"
                                        onclick="openUserModal('edit', JSON.parse(this.dataset.user))"
                                        class="rounded border border-slate-300 px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-slate-100">
                                        Editar
                                    </button>
                                    @unless (auth()->user()?->is($user))
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            class="ml-1 inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded border border-red-300 px-2.5 py-1.5 font-semibold text-red-600 hover:bg-red-50">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showInternalId ? 9 : 8 }}" class="px-4 py-12 text-center">
                                    <i class="fa-solid fa-users mb-3 text-2xl text-slate-300"></i>
                                    <p class="text-sm font-semibold text-slate-700">Todavía no hay usuarios registrados.</p>
                                    <p class="mt-1 text-xs text-slate-400">Usa “Agregar usuario” para crear la primera
                                        cuenta.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="userModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200">
        <div id="userModalPanel"
            class="max-h-[92vh] w-full max-w-2xl scale-95 overflow-y-auto rounded-xl bg-white shadow-2xl transition-transform duration-200">
            <div class="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
                <h2 id="userModalTitle" class="font-bold">Agregar usuario</h2>
                <button type="button" onclick="closeUserModal()" class="text-slate-300 hover:text-white"
                    aria-label="Cerrar">
                    <i class="fa-solid fa-xmark text-lg"></i>
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
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="userPassword" class="mb-1 block text-xs font-semibold text-slate-700">Contraseña <span
                                id="passwordRequirement">*</span></label>
                        <input id="userPassword" name="password" type="password" minlength="8"
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100">
                        <p id="passwordHelp" class="mt-1 text-[11px] text-slate-400">Debe tener al menos 8 caracteres.</p>
                    </div>
                    <div id="passwordConfirmationField">
                        <label for="userPasswordConfirmation"
                            class="mb-1 block text-xs font-semibold text-slate-700">Confirmar contraseña <span
                                id="confirmationRequirement">*</span></label>
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

        document.addEventListener('DOMContentLoaded', () => {
            if (formHasErrors) {
                openUserModal(oldUserValues.user_id ? 'edit' : 'create', oldUserValues);
            }
        });
    </script>
@endsection
