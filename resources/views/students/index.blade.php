@extends('layouts.app')

@section('title', 'Estudiantes')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Estudiantes" :description="$can['manage'] ? 'Vincula las cuentas de estudiante con su carrera, nivel, promedio y habilidades. El coordinador valida cada perfil.' : 'Revisa y valida los datos académicos de los estudiantes de tu carrera.'">
            @if ($can['manage'])
            <x-slot:actions>
                <button type="button" onclick="openCreateModal('createStudentModal')"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo perfil
                </button>
            </x-slot:actions>
            @endif
        </x-page-header>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-stat-card label="Perfiles de estudiante" :value="$counts['total']" />
            <x-stat-card label="Validados por coordinador" :value="$counts['validados']" color="emerald" />
            <x-stat-card label="Por validar" :value="$counts['por_validar']" color="amber" />
        </div>

        <x-table-card title="Listado de estudiantes" :total="$counts['total']" search-id="studentSearch" search-placeholder="Buscar por nombre, ID, carrera o habilidad...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
            </x-slot:icon>

            <table id="studentsTable" class="w-full lg:min-w-[960px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">ID</th>
                        <th class="px-6 py-3.5">Estudiante</th>
                        <th class="px-6 py-3.5">Carrera</th>
                        <th class="px-6 py-3.5">Nivel / año / promedio</th>
                        <th class="px-6 py-3.5">Habilidades</th>
                        <th class="px-6 py-3.5 text-center">Postulaciones</th>
                        <th class="px-6 py-3.5 text-center">Validación</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($students as $student)
                        @php
                            $fullName = trim(($student->user?->name ?? '').' '.($student->user?->last_name ?? '')) ?: 'Sin cuenta';
                            $skills = $student->habilidades ?? [];
                            $rowData = [
                                'id_student' => $student->id_student,
                                'user_id' => $student->user_id,
                                'career_id' => $student->career_id,
                                'nivel_avance' => $student->nivel_avance,
                                'habilidades' => implode(', ', $skills),
                                'year_level' => $student->year_level,
                                'gpa' => $student->gpa,
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4" data-order="{{ $student->id_student }}">
                                <span class="px-2 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono text-[11.5px] font-semibold text-slate-600">#{{ $student->id_student }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-[13.5px]">{{ $fullName }}</p>
                                <p class="text-[12px] text-slate-500">{{ $student->user?->email }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if ($student->career)
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono text-[11px] font-bold text-slate-700">{{ $student->career->codigo }}</span>
                                    <span class="ml-1 text-slate-700">{{ $student->career->nombre }}</span>
                                @else
                                    <span class="text-slate-500">{{ $student->carrera ?: '—' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $student->nivel_avance }}</span>
                                <p class="mt-1 text-[12px] text-slate-500">
                                    {{ $student->year_level ? $student->year_level.'° año' : 'Año sin registrar' }} ·
                                    Promedio <b class="text-slate-700">{{ $student->gpa !== null ? number_format((float) $student->gpa, 1, ',', '') : '—' }}</b>
                                </p>
                            </td>
                            <td class="px-6 py-4" data-search="{{ implode(' ', $skills) }}">
                                <div class="flex flex-wrap gap-1 max-w-[240px]">
                                    @forelse (array_slice($skills, 0, 3) as $skill)
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-[11px] font-semibold text-slate-600">{{ $skill }}</span>
                                    @empty
                                        <span class="text-slate-400 text-[12px]">Sin habilidades</span>
                                    @endforelse
                                    @if (count($skills) > 3)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11px] font-bold text-slate-500" title="{{ implode(', ', array_slice($skills, 3)) }}">+{{ count($skills) - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center" data-order="{{ $student->applications_count }}">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $student->applications_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                    {{ $student->applications_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($student->validated_at)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                                        title="{{ $student->validator ? 'Por '.trim($student->validator->name.' '.$student->validator->last_name) : '' }} el {{ $student->validated_at->format('d-m-Y') }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Validado
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Por validar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($can['validate'] && ! $student->validated_at)
                                        <form id="validate-student-{{ $student->id }}" action="{{ route('students.validate', $student) }}" method="POST">
                                            @csrf
                                            <x-action-button type="approve" title="Validar datos del estudiante"
                                                onclick="confirmSubmit('validate-student-{{ $student->id }}', { title: '¿Validar perfil?', text: {{ json_encode('Confirmas que los datos académicos de '.$fullName.' son correctos.') }}, confirmText: 'Sí, validar', color: '#047857', icon: 'question' })" />
                                        </form>
                                    @endif
                                    @if ($can['manage'])
                                    <x-action-button type="edit" title="Editar perfil"
                                        onclick="openEditModal('editStudentModal', {{ json_encode(route('students.update', $student)) }}, {{ json_encode($rowData) }}, {{ json_encode('#'.$student->id_student.' · '.$fullName) }})" />
                                    <form id="delete-student-{{ $student->id }}" action="{{ route('students.destroy', $student) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-action-button type="delete" title="Eliminar perfil"
                                            onclick="confirmSubmit('delete-student-{{ $student->id }}', { title: '¿Eliminar perfil?', text: {{ json_encode('Se eliminará el perfil de '.$fullName.'. La cuenta de usuario se conserva.') }}, confirmText: 'Sí, eliminar' })" />
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

    <datalist id="nivelesAvance">
        <option value="Inicial"></option>
        <option value="Intermedio"></option>
        <option value="Avanzado"></option>
        <option value="Egresado"></option>
    </datalist>

    @if ($can['manage'])
    @foreach (['create' => 'Nuevo perfil de estudiante', 'edit' => 'Editar perfil de estudiante'] as $mode => $modalTitle)
        @php $modalId = $mode.'StudentModal'; @endphp
        <x-form-modal :id="$modalId" :title="$modalTitle" subtitle="Cuenta, carrera, nivel y habilidades del estudiante.">
            <form action="{{ $mode === 'create' ? route('students.store') : old('_action', '#') }}" method="POST" class="space-y-5 p-6">
                @csrf
                @if ($mode === 'edit') @method('PUT') @endif
                <input type="hidden" name="_modal" value="{{ $modalId }}">
                <input type="hidden" name="_action" value="{{ old('_action') }}">
                <x-form-errors :modal="$modalId" />

                @php $old = fn (string $key, $default = null) => old('_modal') === $modalId ? old($key, $default) : $default; @endphp
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-field label="ID de estudiante" for="{{ $mode }}-id_student" required>
                        <input type="number" id="{{ $mode }}-id_student" name="id_student" required min="1" value="{{ $old('id_student') }}" placeholder="Ej: 20261001" class="form-input font-mono">
                    </x-field>
                    <x-field label="Cuenta de estudiante" for="{{ $mode }}-user_id" required>
                        <select id="{{ $mode }}-user_id" name="user_id" required class="form-input">
                            <option value="">Selecciona una cuenta</option>
                            @foreach ($studentUsers as $account)
                                <option value="{{ $account->id }}" @selected((string) $old('user_id') === (string) $account->id)>
                                    {{ trim($account->name.' '.$account->last_name) }} ({{ $account->email }}){{ in_array($account->id, $usedUserIds) ? ' · ya tiene perfil' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Carrera" for="{{ $mode }}-career_id" required>
                        <select id="{{ $mode }}-career_id" name="career_id" required class="form-input">
                            <option value="">Selecciona una carrera</option>
                            @foreach ($careers as $career)
                                <option value="{{ $career->id }}" @selected((string) $old('career_id') === (string) $career->id)>{{ $career->codigo }} · {{ $career->nombre }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Nivel de avance" for="{{ $mode }}-nivel_avance" required>
                        <input type="text" id="{{ $mode }}-nivel_avance" name="nivel_avance" list="nivelesAvance" required maxlength="120" value="{{ $old('nivel_avance') }}" placeholder="Ej: Avanzado" class="form-input">
                    </x-field>
                    <x-field label="Año que cursa" for="{{ $mode }}-year_level">
                        <select id="{{ $mode }}-year_level" name="year_level" class="form-input">
                            <option value="">Sin registrar</option>
                            @for ($year = 1; $year <= 7; $year++)
                                <option value="{{ $year }}" @selected((string) $old('year_level') === (string) $year)>{{ $year }}° año</option>
                            @endfor
                        </select>
                    </x-field>
                    <x-field label="Promedio de notas" for="{{ $mode }}-gpa" hint="Escala 1,0 a 7,0. Algunas ofertas exigen un promedio mínimo.">
                        <input type="number" id="{{ $mode }}-gpa" name="gpa" min="1" max="7" step="0.1" value="{{ $old('gpa') }}" placeholder="Ej: 5.6" class="form-input">
                    </x-field>
                    <x-field label="Habilidades" for="{{ $mode }}-habilidades" class="sm:col-span-2" hint="Sepáralas con comas. Se usan para calcular la afinidad con las ofertas.">
                        <input type="text" id="{{ $mode }}-habilidades" name="habilidades" value="{{ is_array($old('habilidades')) ? implode(', ', $old('habilidades')) : $old('habilidades') }}" placeholder="PHP, Laravel, SQL" class="form-input">
                    </x-field>
                </div>

                <x-form-actions :modal="$modalId" :label="$mode === 'create' ? 'Crear perfil' : 'Guardar cambios'" />
            </form>
        </x-form-modal>
    @endforeach
    @endif
@endsection

@push('scripts')
    <script>
        initDataTable('#studentsTable', '#studentSearch', { order: [[0, 'asc']] });
    </script>
@endpush
