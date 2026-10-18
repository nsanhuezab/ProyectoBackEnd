@extends('layouts.app')

@section('title', 'Mis postulaciones')

@section('content')
    <section class="space-y-6">
        <header class="border-b border-slate-300 pb-5">
            <p class="text-xs font-bold uppercase text-red-700">Área estudiante</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Mis postulaciones</h1>
        </header>
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto p-3 sm:p-5">
                <table id="applicationsTable" class="w-full text-left text-sm text-slate-700">
                    <thead><tr><th>Oferta</th><th>Empresa</th><th>Carrera</th><th>Estado</th><th>Afinidad</th><th>Fecha</th><th>CV</th><th>Acciones</th></tr></thead>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiUrl = @js($apiUrl);
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const table = $('#applicationsTable').DataTable({
                processing: true, serverSide: true, scrollX: true, pageLength: 10,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                ajax: async (request, callback) => {
                    const order = request.order?.[0] ?? { column: 0, dir: 'desc' };
                    const fields = ['offer', 'company', 'career', 'estado', 'matching_score', 'created_at'];
                    const params = new URLSearchParams({ page: String(Math.floor(request.start / request.length) + 1), per_page: String(request.length), search: request.search.value, sort: fields[order.column] ?? 'id', dir: order.dir });
                    const response = await fetch(`${apiUrl}?${params}`, { headers: { Accept: 'application/json' } });
                    const result = await response.json();
                    callback({ draw: request.draw, recordsTotal: result.meta?.total ?? 0, recordsFiltered: result.meta?.filtered ?? 0, data: result.data ?? [] });
                },
                columns: [
                    ...['offer', 'company', 'career', 'estado', 'matching_score', 'created_at'].map((data) => ({ data, render: (value, type) => type === 'display' ? $.fn.dataTable.render.text().display(String(value ?? '')) : value })),
                    { data: 'cv_url', orderable: false, searchable: false, render: (url, type) => type === 'display' ? `<a class="text-red-700 underline" href="${$.fn.dataTable.render.text().display(url)}">Descargar</a>` : url },
                    { data: null, orderable: false, searchable: false, render: (_value, _type, row) => row.estado === 'Postulado' ? '<button type="button" data-withdraw class="text-red-700 hover:underline">Retirar</button>' : '' },
                ],
            });
            $('#applicationsTable').on('click', '[data-withdraw]', async function () {
                const row = table.row($(this).closest('tr')).data();
                const result = await Swal.fire({ title: '¿Retirar postulación?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Retirar', cancelButtonText: 'Conservar' });
                if (!result.isConfirmed) return;
                const response = await fetch(`${apiUrl}/${row.id}`, { method: 'DELETE', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf } });
                if (!response.ok) {
                    const error = await response.json();
                    Swal.fire({ icon: 'error', title: 'No se pudo retirar', text: error.message ?? 'La operación falló.' });
                    return;
                }
                table.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Postulación retirada', timer: 1500, showConfirmButton: false });
            });
        });
    </script>
@endpush
<div>
    <!-- Simplicity is the essence of happiness. - Cedric Bledsoe -->
</div>
