@extends('layouts.app')

@section('title', 'Coordinadores')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Coordinadores" description="Administra a los coordinadores de práctica y la carrera que tiene a cargo cada uno.">
            <x-slot:actions>
                <button type="button" onclick="openCreateModal('createCoordinatorModal')"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo coordinador
                </button>
            </x-slot:actions>
        </x-page-header>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-stat-card label="Coordinadores" :value="$counts['total']" />
            <x-stat-card label="Carreras cubiertas" :value="$counts['carreras']" color="emerald" />
            <x-stat-card label="Sin carrera asignada" :value="$counts['sin_carrera']" color="amber" />
        </div>

        <x-table-card title="Listado de coordinadores" :total="$counts['total']" search-id="coordinatorSearch" search-placeholder="Buscar por nombre, correo o carrera...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </x-slot:icon>

            <table id="coordinatorsTable" class="w-full min-w-[780px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Coordinador</th>
                        <th class="px-6 py-3.5">Correo</th>
                        <th class="px-6 py-3.5">Teléfono</th>
                        <th class="px-6 py-3.5">Carrera a cargo</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($coordinators as $coordinator)
                        @php
                            $fullName = trim($coordinator->name.' '.$coordinator->last_name);
                            $initials = mb_strtoupper(mb_substr($coordinator->name, 0, 1).mb_substr($coordinator->last_name ?? '', 0, 1));
                            $rowData = [
                                'nombre' => $coordinator->name,
                                'apellido' => $coordinator->last_name,
                                'correo' => $coordinator->email,
                                'telefono' => $coordinator->telefono,
                                'career_id' => $coordinator->career_id,
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center text-[12px] font-bold shrink-0">{{ $initials }}</span>
                                    <span class="font-bold text-slate-800 text-[13.5px]">{{ $fullName }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="mailto:{{ $coordinator->email }}" class="text-slate-700 hover:text-red-700">{{ $coordinator->email }}</a>
                            </td>
                            <td class="px-6 py-4 font-mono text-[12px]">{{ $coordinator->telefono ?: '—' }}</td>
                            <td class="px-6 py-4">
                                @if ($coordinator->career)
                                    <span class="inline-flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono text-[11px] font-bold text-slate-700">{{ $coordinator->career->codigo }}</span>
                                        <span class="text-slate-800 font-medium">{{ $coordinator->career->nombre }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Sin carrera</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-action-button type="edit" title="Editar coordinador"
                                        onclick="openEditModal('editCoordinatorModal', {{ json_encode(route('coordinators.update', $coordinator)) }}, {{ json_encode($rowData) }}, {{ json_encode($fullName) }})" />
                                    <form id="delete-coordinator-{{ $coordinator->id }}" action="{{ route('coordinators.destroy', $coordinator) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-action-button type="delete" title="Eliminar coordinador"
                                            onclick="confirmSubmit('delete-coordinator-{{ $coordinator->id }}', { title: '¿Eliminar coordinador?', text: {{ json_encode('Se eliminará la cuenta de '.$fullName.'. Esta acción no se puede deshacer.') }}, confirmText: 'Sí, eliminar' })" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-card>
    </div>

    @foreach (['create' => 'Nuevo coordinador', 'edit' => 'Editar coordinador'] as $mode => $modalTitle)
        @php $modalId = $mode.'CoordinatorModal'; @endphp
        <x-form-modal :id="$modalId" :title="$modalTitle" subtitle="Datos de contacto y carrera a cargo.">
            <form action="{{ $mode === 'create' ? route('coordinators.store') : old('_action', '#') }}" method="POST" class="space-y-5 p-6">
                @csrf
                @if ($mode === 'edit') @method('PUT') @endif
                <input type="hidden" name="_modal" value="{{ $modalId }}">
                <input type="hidden" name="_action" value="{{ old('_action') }}">
                <x-form-errors :modal="$modalId" />

                @php $old = fn (string $key, $default = null) => old('_modal') === $modalId ? old($key, $default) : $default; @endphp
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-field label="Nombre" for="{{ $mode }}-nombre" required>
                        <input type="text" id="{{ $mode }}-nombre" name="nombre" required maxlength="255" value="{{ $old('nombre') }}" class="form-input">
                    </x-field>
                    <x-field label="Apellido" for="{{ $mode }}-apellido">
                        <input type="text" id="{{ $mode }}-apellido" name="apellido" maxlength="255" value="{{ $old('apellido') }}" class="form-input">
                    </x-field>
                    <x-field label="Correo institucional" for="{{ $mode }}-correo" required>
                        <input type="email" id="{{ $mode }}-correo" name="correo" required maxlength="255" value="{{ $old('correo') }}" placeholder="nombre@ucsc.cl" class="form-input">
                    </x-field>
                    <x-field label="Teléfono" for="{{ $mode }}-telefono">
                        <input type="text" id="{{ $mode }}-telefono" name="telefono" maxlength="20" value="{{ $old('telefono') }}" placeholder="+56 9 1234 5678" class="form-input">
                    </x-field>
                    <x-field label="Carrera a cargo" for="{{ $mode }}-career_id" required class="sm:col-span-2">
                        <select id="{{ $mode }}-career_id" name="career_id" required class="form-input">
                            <option value="">Selecciona una carrera</option>
                            @foreach ($careers as $career)
                                <option value="{{ $career->id }}" @selected((string) $old('career_id') === (string) $career->id)>{{ $career->codigo }} · {{ $career->nombre }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <x-form-actions :modal="$modalId" :label="$mode === 'create' ? 'Registrar coordinador' : 'Guardar cambios'" />
            </form>
        </x-form-modal>
    @endforeach
@endsection

@push('scripts')
    <script>
        initDataTable('#coordinatorsTable', '#coordinatorSearch', { order: [[0, 'asc']] });
    </script>
@endpush
