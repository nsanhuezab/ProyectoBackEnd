@foreach($controller->rowActions($row) as $action)
    @php
        $style = ['success' => 'bg-green-600 text-white hover:bg-green-700', 'danger' => 'bg-red-600 text-white hover:bg-red-700', 'primary' => 'bg-ucsc-700 text-white hover:bg-ucsc-800'][$action['style'] ?? 'primary'] ?? 'bg-ucsc-700 text-white hover:bg-ucsc-800';
    @endphp

    @if(($action['method'] ?? 'GET') === 'GET')
        <a href="{{ $action['url'] }}" class="btn btn-sm {{ $style }}">{{ $action['label'] }}</a>
    @else
        <form method="POST" action="{{ $action['url'] }}">
            @csrf
            @method($action['method'])
            @foreach($action['data'] ?? [] as $k => $val)
                <input type="hidden" name="{{ $k }}" value="{{ $val }}">
            @endforeach
            <button type="submit" class="btn btn-sm {{ $style }}">{{ $action['label'] }}</button>
        </form>
    @endif
@endforeach

@if($controller->canEdit($row))
    <a href="{{ route($base.'.edit', $row) }}" class="btn-secondary btn-sm" title="Editar {{ $labels['singular'] }}">
        <x-icon name="pencil" class="h-4 w-4" /> Editar
    </a>
@endif

@if($controller->canDelete($row))
    <form method="POST" action="{{ route($base.'.destroy', $row) }}"
          onsubmit="return confirm('¿Eliminar {{ $labels['g'] === 'a' ? 'esta' : 'este' }} {{ $labels['singular'] }}? Esta acción no se puede deshacer.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm bg-white text-red-600 ring-1 ring-inset ring-red-200 hover:bg-red-50" title="Eliminar {{ $labels['singular'] }}">
            <x-icon name="trash" class="h-4 w-4" /> Eliminar
        </button>
    </form>
@endif
