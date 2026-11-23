@extends('layouts.app')

@section('title', 'Historial de cambios')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Historial de cambios"
            description="Registro de quién creó, modificó o eliminó información en la plataforma. No se puede editar." />

        <x-table-card title="Últimos movimientos" :total="$logs->count()" search-id="activitySearch" search-placeholder="Buscar por usuario, módulo o registro...">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </x-slot:icon>

            <table id="activityTable" class="w-full lg:min-w-[960px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Fecha</th>
                        <th class="px-6 py-3.5">Usuario</th>
                        <th class="px-6 py-3.5">Acción</th>
                        <th class="px-6 py-3.5">Módulo</th>
                        <th class="px-6 py-3.5">Registro</th>
                        <th class="px-6 py-3.5">Cambios</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($logs as $log)
                        @php
                            $actionStyle = [
                                'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'updated' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'deleted' => 'bg-red-50 text-red-700 border-red-200',
                            ][$log->action] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                            $changes = collect($log->changes ?? [])->map(function ($value, $field) use ($log) {
                                $show = fn ($v) => is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : (string) ($v ?? '—');
                                return $log->action === 'updated' && is_array($value) && array_key_exists('antes', $value)
                                    ? $field.': '.$show($value['antes']).' → '.$show($value['después'] ?? null)
                                    : $field.': '.$show($value);
                            });
                        @endphp
                        <tr class="align-top hover:bg-gray-50/70">
                            <td class="px-6 py-3 whitespace-nowrap" data-order="{{ $log->created_at?->timestamp }}">{{ $log->created_at?->format('d-m-Y H:i') }}</td>
                            <td class="px-6 py-3">
                                <p class="font-semibold text-slate-800">{{ $log->user ? trim($log->user->name.' '.$log->user->last_name) : 'Sistema' }}</p>
                                <p class="text-[11.5px] text-slate-500">{{ $log->user?->email }}</p>
                            </td>
                            <td class="px-6 py-3"><span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold border {{ $actionStyle }}">{{ $log->actionName() }}</span></td>
                            <td class="px-6 py-3 font-medium text-slate-700">{{ $log->moduleName() }}</td>
                            <td class="px-6 py-3">{{ $log->description }}</td>
                            <td class="px-6 py-3">
                                @if ($changes->isNotEmpty())
                                    <details>
                                        <summary class="cursor-pointer text-[12px] font-semibold text-red-700">{{ $changes->count() }} campo(s)</summary>
                                        <ul class="mt-1 space-y-0.5 text-[11.5px] text-slate-600 max-w-[360px] break-words">
                                            @foreach ($changes as $line)
                                                <li>{{ \Illuminate\Support\Str::limit($line, 160) }}</li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-card>
    </div>
@endsection

@push('scripts')
    <script>
        initDataTable('#activityTable', '#activitySearch', { order: [[0, 'desc']], pageLength: 25 });
    </script>
@endpush
