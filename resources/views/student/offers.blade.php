@extends('layouts.app')

@section('title', 'Explorar ofertas')

@section('content')
    <section class="space-y-6">
        <header class="border-b border-slate-300 pb-5">
            <p class="text-xs font-bold uppercase text-red-700">Prácticas profesionales</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Ofertas disponibles</h1>
        </header>

        <div class="grid gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-4">
            <select id="filterCareer" class="rounded-md border-slate-300 text-sm" aria-label="Filtrar por carrera">
                <option value="">Todas las carreras</option>
                @foreach ($careers as $id => $career)
                    <option value="{{ $id }}">{{ $career }}</option>
                @endforeach
            </select>
            <input id="filterArea" class="rounded-md border-slate-300 text-sm" placeholder="Área" aria-label="Filtrar por área">
            <select id="filterModality" class="rounded-md border-slate-300 text-sm" aria-label="Filtrar por modalidad">
                <option value="">Todas las modalidades</option>
                <option value="Presencial">Presencial</option>
                <option value="Remota">Remota</option>
                <option value="Híbrida">Híbrida</option>
            </select>
            <input id="filterLocation" class="rounded-md border-slate-300 text-sm" placeholder="Ubicación" aria-label="Filtrar por ubicación">
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto p-3 sm:p-5">
                <table id="offersTable" class="w-full min-w-[820px] whitespace-nowrap text-left text-sm text-slate-700">
                    <thead><tr>
                        <th>Oferta</th><th>Empresa</th><th>Carreras</th><th>Área</th><th>Modalidad</th>
                        <th>Duración</th><th>Ubicación</th><th>Cupos</th><th>Acción</th>
                    </tr></thead>
                </table>
            </div>
        </div>
    </section>

    <div id="applicationModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-4" role="dialog" aria-modal="true" aria-labelledby="applicationTitle">
        <div class="w-full max-w-xl rounded-lg bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 id="applicationTitle" class="font-bold text-slate-900">Postular</h2>
                <button type="button" id="closeApplication" class="rounded p-2 text-slate-500 hover:bg-slate-100" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            </div>
            <form id="applicationForm" class="space-y-4 p-5">
                <input type="hidden" name="offer_id" id="applicationOfferId">
                <div>
                    <label for="cv" class="mb-1 block text-sm font-semibold text-slate-700">Currículum (PDF, DOC o DOCX)</label>
                    <input id="cv" name="cv" type="file" accept=".pdf,.doc,.docx" required class="w-full rounded-md border border-slate-300 p-2 text-sm">
                </div>
                <div>
                    <label for="motivation" class="mb-1 block text-sm font-semibold text-slate-700">Carta de motivación</label>
                    <textarea id="motivation" name="carta_motivacion" rows="5" maxlength="10000" required class="w-full rounded-md border border-slate-300 p-3 text-sm"></textarea>
                </div>
                <p id="applicationError" class="hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800" role="alert"></p>
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" id="cancelApplication" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancelar</button>
                    <button type="submit" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Enviar postulación</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiUrl = @js($apiUrl);
            const applyUrl = @js($applyUrl);
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const filters = ['filterCareer', 'filterArea', 'filterModality', 'filterLocation'];
            const table = $('#offersTable').DataTable({
                processing: true, serverSide: true, scrollX: true, pageLength: 10,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                ajax: async (request, callback) => {
                    const order = request.order?.[0] ?? { column: 0, dir: 'asc' };
                    const fields = ['titulo', 'empresa', 'carreras', 'area', 'modalidad', 'duracion_semanas', 'ubicacion', 'vacantes'];
                    const params = new URLSearchParams({
                        page: String(Math.floor(request.start / request.length) + 1), per_page: String(request.length),
                        search: request.search.value, sort: fields[order.column] ?? 'id', dir: order.dir,
                        career_id: document.getElementById('filterCareer').value,
                        area: document.getElementById('filterArea').value,
                        modalidad: document.getElementById('filterModality').value,
                        ubicacion: document.getElementById('filterLocation').value,
                    });
                    const response = await fetch(`${apiUrl}?${params}`, { headers: { Accept: 'application/json' } });
                    const result = await response.json();
                    callback({ draw: request.draw, recordsTotal: result.meta?.total ?? 0, recordsFiltered: result.meta?.filtered ?? 0, data: result.data ?? [] });
                },
                columns: [
                    ...['titulo', 'empresa', 'carreras', 'area', 'modalidad', 'duracion_semanas', 'ubicacion', 'vacantes'].map((data) => ({
                        data, render: (value, type) => {
                            const text = Array.isArray(value) ? value.join(', ') : (data === 'duracion_semanas' ? `${value ?? ''} semanas` : (value ?? ''));
                            return type === 'display' ? $.fn.dataTable.render.text().display(String(text)) : text;
                        },
                    })),
                    { data: null, orderable: false, searchable: false, render: () => '<button type="button" data-apply class="whitespace-nowrap rounded-md bg-red-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-800">Postular</button>' },
                ],
            });

            filters.forEach((id) => document.getElementById(id).addEventListener('input', () => table.ajax.reload()));
            const modal = document.getElementById('applicationModal');
            const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
            document.getElementById('closeApplication').addEventListener('click', closeModal);
            document.getElementById('cancelApplication').addEventListener('click', closeModal);
            $('#offersTable').on('click', '[data-apply]', function () {
                const offer = table.row($(this).closest('tr')).data();
                document.getElementById('applicationOfferId').value = offer.id;
                document.getElementById('applicationTitle').textContent = `Postular a ${offer.titulo}`;
                modal.classList.remove('hidden'); modal.classList.add('flex');
            });
            document.getElementById('applicationForm').addEventListener('submit', async (event) => {
                event.preventDefault();
                const response = await fetch(applyUrl, {
                    method: 'POST', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: new FormData(event.currentTarget),
                });
                const result = await response.json();
                if (!response.ok) {
                    const error = document.getElementById('applicationError');
                    error.textContent = Object.values(result.errors ?? {}).flat().join(' ') || result.message || 'No se pudo enviar la postulación.';
                    error.classList.remove('hidden');
                    return;
                }
                closeModal(); event.currentTarget.reset();
                await Swal.fire({ icon: 'success', title: 'Postulación enviada', text: 'Tu CV quedó resguardado en el sistema.' });
                table.ajax.reload(null, false);
            });
        });
    </script>
@endpush
<div>
</div>
