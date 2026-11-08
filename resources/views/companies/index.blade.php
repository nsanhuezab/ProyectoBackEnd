@extends('layouts.app')

@section('title', 'Empresas')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Empresas" description="Registro de empresas en convenio y del supervisor que evalúa la práctica.">
            <x-slot:actions>
                <button type="button" onclick="openCreateModal('createCompanyModal')"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nueva empresa
                </button>
            </x-slot:actions>
        </x-page-header>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-stat-card label="Empresas registradas" :value="$counts['total']" />
            <x-stat-card label="Con ofertas publicadas" :value="$counts['con_ofertas']" color="emerald" />
            <x-stat-card label="Con cuenta en la plataforma" :value="$counts['con_cuenta']" color="blue" />
        </div>

        <x-table-card title="Listado de empresas" :total="$counts['total']" search-id="companySearch" search-placeholder="Buscar por razón social, RUT, rubro o supervisor...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </x-slot:icon>

            <table id="companiesTable" class="w-full min-w-[960px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Empresa</th>
                        <th class="px-6 py-3.5">RUT</th>
                        <th class="px-6 py-3.5">Supervisor de práctica</th>
                        <th class="px-6 py-3.5 text-center">Ofertas</th>
                        <th class="px-6 py-3.5 text-center">Cuenta</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($companies as $company)
                        @php
                            $ownerName = $company->owner ? trim($company->owner->name.' '.$company->owner->last_name) : null;
                            $rowData = $company->only(['owner_user_id', 'razon_social', 'rut', 'rubro', 'direccion', 'sitio_web', 'contacto_nombre', 'contacto_email', 'telefono']);
                            $showData = $rowData + [
                                'cuenta' => $ownerName ? $ownerName.' ('.$company->owner->email.')' : 'Sin cuenta vinculada',
                                'ofertas' => $company->offers_count,
                                'registrada' => $company->created_at?->format('d-m-Y'),
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-[13.5px]">{{ $company->razon_social }}</p>
                                <p class="text-[12px] text-slate-500">{{ $company->rubro }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-[12px] text-slate-700">{{ $company->rut }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-800">{{ $company->contacto_nombre }}</p>
                                <p class="text-[12px] text-slate-500">{{ $company->contacto_email }}</p>
                            </td>
                            <td class="px-6 py-4 text-center" data-order="{{ $company->offers_count }}">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $company->offers_count > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                    {{ $company->offers_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($ownerName)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="{{ $ownerName }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Vinculada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Sin cuenta
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-action-button type="view" title="Ver detalle"
                                        onclick="openShowModal('showCompanyModal', {{ json_encode($showData) }})" />
                                    <x-action-button type="edit" title="Editar empresa"
                                        onclick="openEditModal('editCompanyModal', {{ json_encode(route('companies.update', $company)) }}, {{ json_encode($rowData) }}, {{ json_encode($company->razon_social) }})" />
                                    <form id="delete-company-{{ $company->id }}" action="{{ route('companies.destroy', $company) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-action-button type="delete" title="Eliminar empresa"
                                            onclick="confirmSubmit('delete-company-{{ $company->id }}', { title: '¿Eliminar empresa?', text: {{ json_encode('Se eliminará '.$company->razon_social.'. Esta acción no se puede deshacer.') }}, confirmText: 'Sí, eliminar' })" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-card>
    </div>

    {{-- Modal: detalle de la empresa --}}
    <x-form-modal id="showCompanyModal" title="Detalle de la empresa" width="max-w-lg">
        <dl class="divide-y divide-slate-100 px-6 py-2 text-[13px]">
            @php
                $detailFields = [
                    'razon_social' => 'Razón social', 'rut' => 'RUT', 'rubro' => 'Rubro', 'direccion' => 'Dirección',
                    'sitio_web' => 'Sitio web', 'contacto_nombre' => 'Supervisor', 'contacto_email' => 'Correo del supervisor',
                    'telefono' => 'Teléfono', 'cuenta' => 'Cuenta en la plataforma', 'ofertas' => 'Ofertas publicadas', 'registrada' => 'Registrada el',
                ];
            @endphp
            @foreach ($detailFields as $key => $label)
                <div class="flex justify-between gap-6 py-2.5">
                    <dt class="text-slate-500 shrink-0">{{ $label }}</dt>
                    <dd data-show="{{ $key }}" class="font-semibold text-slate-800 text-right break-all"></dd>
                </div>
            @endforeach
        </dl>
    </x-form-modal>

    @foreach (['create' => 'Nueva empresa', 'edit' => 'Editar empresa'] as $mode => $modalTitle)
        @php $modalId = $mode.'CompanyModal'; @endphp
        <x-form-modal :id="$modalId" :title="$modalTitle" subtitle="Datos de la empresa y del supervisor de práctica." width="max-w-2xl">
            <form action="{{ $mode === 'create' ? route('companies.store') : old('_action', '#') }}" method="POST" class="space-y-5 p-6">
                @csrf
                @if ($mode === 'edit') @method('PUT') @endif
                <input type="hidden" name="_modal" value="{{ $modalId }}">
                <input type="hidden" name="_action" value="{{ old('_action') }}">
                <x-form-errors :modal="$modalId" />

                @php $old = fn (string $key, $default = null) => old('_modal') === $modalId ? old($key, $default) : $default; @endphp

                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Empresa</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-field label="Razón social" for="{{ $mode }}-razon_social" required class="sm:col-span-2">
                        <input type="text" id="{{ $mode }}-razon_social" name="razon_social" required maxlength="255" value="{{ $old('razon_social') }}" class="form-input">
                    </x-field>
                    <x-field label="RUT" for="{{ $mode }}-rut" required>
                        <input type="text" id="{{ $mode }}-rut" name="rut" required maxlength="15" value="{{ $old('rut') }}" placeholder="76123456-7" class="form-input font-mono">
                    </x-field>
                    <x-field label="Rubro" for="{{ $mode }}-rubro" required>
                        <input type="text" id="{{ $mode }}-rubro" name="rubro" required maxlength="120" value="{{ $old('rubro') }}" placeholder="Tecnología" class="form-input">
                    </x-field>
                    <x-field label="Dirección" for="{{ $mode }}-direccion" required>
                        <input type="text" id="{{ $mode }}-direccion" name="direccion" required maxlength="255" value="{{ $old('direccion') }}" class="form-input">
                    </x-field>
                    <x-field label="Sitio web" for="{{ $mode }}-sitio_web">
                        <input type="url" id="{{ $mode }}-sitio_web" name="sitio_web" maxlength="255" value="{{ $old('sitio_web') }}" placeholder="https://" class="form-input">
                    </x-field>
                    <x-field label="Cuenta de empresa en la plataforma" for="{{ $mode }}-owner_user_id" class="sm:col-span-2" hint="Opcional. Permite que la empresa publique sus propias ofertas.">
                        <select id="{{ $mode }}-owner_user_id" name="owner_user_id" class="form-input">
                            <option value="">Sin cuenta vinculada</option>
                            @foreach ($companyUsers as $account)
                                <option value="{{ $account->id }}" @selected((string) $old('owner_user_id') === (string) $account->id)>{{ trim($account->name.' '.$account->last_name) }} ({{ $account->email }})</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 pt-2">Supervisor de práctica</p>
                <div class="grid gap-4 sm:grid-cols-3">
                    <x-field label="Nombre" for="{{ $mode }}-contacto_nombre" required>
                        <input type="text" id="{{ $mode }}-contacto_nombre" name="contacto_nombre" required maxlength="255" value="{{ $old('contacto_nombre') }}" class="form-input">
                    </x-field>
                    <x-field label="Correo" for="{{ $mode }}-contacto_email" required>
                        <input type="email" id="{{ $mode }}-contacto_email" name="contacto_email" required maxlength="255" value="{{ $old('contacto_email') }}" class="form-input">
                    </x-field>
                    <x-field label="Teléfono" for="{{ $mode }}-telefono">
                        <input type="text" id="{{ $mode }}-telefono" name="telefono" maxlength="30" value="{{ $old('telefono') }}" class="form-input">
                    </x-field>
                </div>

                <x-form-actions :modal="$modalId" :label="$mode === 'create' ? 'Registrar empresa' : 'Guardar cambios'" />
            </form>
        </x-form-modal>
    @endforeach
@endsection

@push('scripts')
    <script>
        initDataTable('#companiesTable', '#companySearch', { order: [[0, 'asc']] });
    </script>
@endpush
