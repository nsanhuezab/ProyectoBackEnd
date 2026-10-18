@extends('layouts.app')

@section('title', 'Seguimiento de prácticas')

@section('content')
    <section class="space-y-6">
        <header class="border-b border-slate-300 pb-5">
            <p class="text-xs font-bold uppercase text-red-700">Área estudiante</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Seguimiento de prácticas</h1>
        </header>
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto p-3 sm:p-5">
                <table id="practicesTable" class="w-full text-left text-sm text-slate-700">
                    <thead><tr><th>Oferta</th><th>Empresa</th><th>Inicio</th><th>Término</th><th>Estado</th><th>Nota</th><th>Acciones</th></tr></thead>
                </table>
            </div>
        </div>
    </section>

    <div id="trackingModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-4" role="dialog" aria-modal="true" aria-labelledby="trackingTitle">
        <div class="w-full max-w-xl rounded-lg bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 id="trackingTitle" class="font-bold text-slate-900">Registrar avance</h2>
                <button type="button" id="closeTracking" class="rounded p-2 text-slate-500 hover:bg-slate-100" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            </div>
            <form id="trackingForm" class="space-y-4 p-5">
                <input type="hidden" id="trackingPracticeId">
                <div id="logFields" class="space-y-4">
                    <div><label for="logDate" class="mb-1 block text-sm font-semibold text-slate-700">Fecha</label><input id="logDate" type="date" required max="{{ now()->toDateString() }}" class="w-full rounded-md border-slate-300 text-sm"></div>
                    <div><label for="logProgress" class="mb-1 block text-sm font-semibold text-slate-700">Avance quincenal</label><textarea id="logProgress" rows="4" maxlength="5000" required class="w-full rounded-md border-slate-300 text-sm"></textarea></div>
                    <div><label for="logCompetencies" class="mb-1 block text-sm font-semibold text-slate-700">Competencias desarrolladas</label><input id="logCompetencies" class="w-full rounded-md border-slate-300 text-sm" placeholder="Comunicación, análisis"></div>
                </div>
                <div id="feedbackFields" class="hidden space-y-4">
                    <div><label for="studentScore" class="mb-1 block text-sm font-semibold text-slate-700">Evaluación de la experiencia (1 a 7)</label><input id="studentScore" type="number" min="1" max="7" step="0.1" class="w-full rounded-md border-slate-300 text-sm"></div>
                    <div><label for="studentComments" class="mb-1 block text-sm font-semibold text-slate-700">Comentarios</label><textarea id="studentComments" rows="4" maxlength="5000" class="w-full rounded-md border-slate-300 text-sm"></textarea></div>
                </div>
                <p id="trackingError" class="hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800" role="alert"></p>
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" id="cancelTracking" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancelar</button>
                    <button type="submit" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiUrl = @js($apiUrl);
            const logsUrl = @js($logsUrl);
            const evaluationUrl = @js($evaluationUrl);
            const today = @js(today()->toDateString());
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const table = $('#practicesTable').DataTable({
                processing: true, serverSide: true, scrollX: true, pageLength: 10,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                ajax: async (request, callback) => {
                    const params = new URLSearchParams({ page: String(Math.floor(request.start / request.length) + 1), per_page: String(request.length), search: request.search.value, sort: 'id', dir: 'desc' });
                    const response = await fetch(`${apiUrl}?${params}`, { headers: { Accept: 'application/json' } });
                    const result = await response.json();
                    callback({ draw: request.draw, recordsTotal: result.meta?.total ?? 0, recordsFiltered: result.meta?.filtered ?? 0, data: result.data ?? [] });
                },
                columns: [
                    ...['oferta', 'empresa', 'fecha_inicio', 'fecha_termino', 'estado', 'calificacion_final'].map((data) => ({ data, render: (value, type) => type === 'display' ? $.fn.dataTable.render.text().display(String(value ?? '—')) : value })),
                    { data: null, orderable: false, searchable: false, render: (_value, _type, row) => {
                        const actions = ['<button type="button" data-action="history" class="mr-2 text-slate-700 hover:underline">Bitácora</button>'];
                        if (row.estado === 'En Curso') actions.push('<button type="button" data-action="log" class="mr-2 text-red-700 hover:underline">Registrar avance</button>');
                        if (row.estado === 'Finalizada') actions.push(`<a class="mr-2 text-red-700 underline" href="${$.fn.dataTable.render.text().display(row.acta_url)}">Acta</a><button type="button" data-action="feedback" class="text-slate-700 hover:underline">Evaluar empresa</button>`);
                        return `<div class="whitespace-nowrap">${actions.join('')}</div>`;
                    } },
                ],
            });

            const modal = document.getElementById('trackingModal');
            const form = document.getElementById('trackingForm');
            let mode = 'log';
            const openModal = (row, nextMode) => {
                mode = nextMode;
                document.getElementById('trackingPracticeId').value = row.id;
                document.getElementById('trackingTitle').textContent = mode === 'log' ? 'Registrar avance quincenal' : 'Evaluar experiencia de práctica';
                document.getElementById('logFields').classList.toggle('hidden', mode !== 'log');
                document.getElementById('feedbackFields').classList.toggle('hidden', mode !== 'feedback');
                document.getElementById('trackingError').classList.add('hidden');
                form.reset();
                if (mode === 'log') document.getElementById('logDate').value = today;
                modal.classList.remove('hidden'); modal.classList.add('flex');
            };
            const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
            document.getElementById('closeTracking').addEventListener('click', closeModal);
            document.getElementById('cancelTracking').addEventListener('click', closeModal);
            $('#practicesTable').on('click', '[data-action]', async function () {
                const row = table.row($(this).closest('tr')).data();
                if (this.dataset.action === 'log' || this.dataset.action === 'feedback') {
                    openModal(row, this.dataset.action === 'log' ? 'log' : 'feedback');
                    return;
                }
                const response = await fetch(`${logsUrl}/${row.id}/logs?per_page=100&sort=fecha&dir=desc`, { headers: { Accept: 'application/json' } });
                const result = await response.json();
                const entries = (result.data ?? []).map((log) => `${log.fecha}: ${log.avance}`).join('\n\n') || 'Aún no hay registros de bitácora.';
                Swal.fire({ title: 'Bitácora de práctica', text: entries, width: 640 });
            });
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const practiceId = document.getElementById('trackingPracticeId').value;
                const isLog = mode === 'log';
                const url = isLog ? `${evaluationUrl}/${practiceId}/logs` : `${evaluationUrl}/${practiceId}/evaluations`;
                const body = isLog ? {
                    fecha: document.getElementById('logDate').value,
                    avance: document.getElementById('logProgress').value,
                    competencias: document.getElementById('logCompetencies').value.split(',').map((value) => value.trim()).filter(Boolean),
                } : {
                    tipo: 'estudiante',
                    calificacion: Number(document.getElementById('studentScore').value),
                    comentarios: document.getElementById('studentComments').value,
                };
                const response = await fetch(url, { method: 'POST', headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body) });
                const result = await response.json();
                if (!response.ok) {
                    const error = document.getElementById('trackingError');
                    error.textContent = Object.values(result.errors ?? {}).flat().join(' ') || result.message || 'No se pudo guardar el registro.';
                    error.classList.remove('hidden');
                    return;
                }
                closeModal(); table.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: isLog ? 'Avance registrado' : 'Evaluación enviada', timer: 1600, showConfirmButton: false });
            });
        });
    </script>
@endpush
<div>
    <!-- Always remember that you are absolutely unique. Just like everyone else. - Margaret Mead -->
</div>
