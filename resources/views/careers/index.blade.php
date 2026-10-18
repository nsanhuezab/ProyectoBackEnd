@extends('layouts.app')

@section('title', 'Gestión de Carreras')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Listado de Carreras</h2>
        <a href="#" class="px-4 py-2 bg-red-700 text-white font-medium rounded-lg hover:bg-red-800 transition-colors">
            Nueva Carrera
        </a>
    </div>

    <!-- Tabla con id="careersTable" -->
    <table id="careersTable" class="w-full text-sm text-left text-slate-600">
        <thead class="text-xs text-slate-700 uppercase bg-slate-100">
            <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Código</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($careers as $career)
            <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-900">{{ $career->id }}</td>
                <td class="px-4 py-3">{{ $career->codigo }}</td>
                <td class="px-4 py-3 font-semibold text-slate-800">{{ $career->nombre }}</td>
                <td class="px-4 py-3">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $career->activa ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ $career->activa ? 'Activa' : 'Inactiva' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <button class="text-blue-600 hover:text-blue-800 font-medium mr-2">Editar</button>
                    <button onclick="confirmDelete('delete-form-{{ $career->id }}')" class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>

                    <form id="delete-form-{{ $career->id }}" action="{{ route('admin.careers.destroy', $career) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Script de inicialización de DataTables -->
<script>
    $(document).ready(function() {
        $('#careersTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            pageLength: 10,
            responsive: true
        });
    });
</script>
@endsection
