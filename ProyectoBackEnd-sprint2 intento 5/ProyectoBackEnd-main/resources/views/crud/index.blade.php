<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <x-page-header :eyebrow="$labels['eyebrow']" :title="$labels['plural']" :description="$labels['description']">
            @if($controller->canCreate())
                <a href="{{ route($base.'.create') }}" class="btn-primary">
                    <x-icon name="plus" class="h-4 w-4" /> {{ $labels['nuevo'] }}
                </a>
            @endif
        </x-page-header>

        <x-flash />

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">

            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 px-4 py-3 text-sm text-gray-600">
                <span>{{ $labels['plural'] }} registrados: <strong class="text-gray-900">{{ $rows->total() }}</strong></span>

                @if($q !== '')
                    <span class="flex items-center gap-2">
                        Resultados para “{{ $q }}”
                        <a href="{{ route($base.'.index') }}" class="font-medium text-ucsc-700 hover:underline">Limpiar</a>
                    </span>
                @endif
            </div>

            @if($rows->isEmpty())
                <div class="px-6 py-14 text-center text-sm text-gray-500">
                    No hay registros para mostrar.
                </div>
            @else
                {{-- Escritorio / tablet: tabla --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach($columns as $col)
                                    <th scope="col" class="{{ ['lg' => 'hidden lg:table-cell', 'xl' => 'hidden xl:table-cell'][$col['hide'] ?? ''] ?? '' }} px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        {{ $col['label'] }}
                                    </th>
                                @endforeach
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rows as $row)
                                <tr class="hover:bg-gray-50">
                                    @foreach($columns as $col)
                                        @php $v = $col['value']($row); @endphp
                                        <td class="{{ ['lg' => 'hidden lg:table-cell', 'xl' => 'hidden xl:table-cell'][$col['hide'] ?? ''] ?? '' }} px-4 py-3 text-gray-700 {{ $loop->first ? 'font-medium text-gray-900' : '' }}">
                                            @if(! empty($col['badge']))
                                                <x-badge :value="$v" :label="is_array($col['badge']) ? ($col['badge'][$v] ?? $v) : $v" />
                                            @else
                                                {{ $v }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            @include('crud._actions')
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Móvil: tarjetas --}}
                <ul class="divide-y divide-gray-100 md:hidden">
                    @foreach($rows as $row)
                        <li class="space-y-3 p-4">
                            <dl class="space-y-1.5 text-sm">
                                @foreach($columns as $col)
                                    @php $v = $col['value']($row); @endphp
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="shrink-0 text-gray-500">{{ $col['label'] }}</dt>
                                        <dd class="min-w-0 break-words text-right {{ $loop->first ? 'font-semibold text-gray-900' : 'text-gray-700' }}">
                                            @if(! empty($col['badge']))
                                                <x-badge :value="$v" :label="is_array($col['badge']) ? ($col['badge'][$v] ?? $v) : $v" />
                                            @else
                                                {{ $v }}
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-3">
                                @include('crud._actions')
                            </div>
                        </li>
                    @endforeach
                </ul>

                @if($rows->hasPages())
                    <div class="border-t border-gray-100 px-4 py-3">
                        {{ $rows->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
