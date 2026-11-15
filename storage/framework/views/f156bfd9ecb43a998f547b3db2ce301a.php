<?php $__env->startSection('title', $cfg['title']); ?>

<?php
    $inputClass = 'w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100 transition';
    $storeUrl = route("catalogs.$catalog.store");
    $rowData = $rows->mapWithKeys(fn ($r) => [$r['id'] => ['values' => $r['values'], 'update_url' => $r['update_url']]]);
    $oldMode = old('_mode');
?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">

        <nav class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500" aria-label="Ruta">
            <span>Panel de administración</span>
            <span>›</span>
            <span class="font-medium text-slate-800"><?php echo e($cfg['title']); ?></span>
        </nav>

        <header class="flex flex-col gap-4 border-b-2 border-slate-800/80 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">Panel de administración</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900"><?php echo e($cfg['title']); ?></h1>
                <p class="mt-1 text-sm text-slate-500"><?php echo e($cfg['description']); ?></p>
            </div>
            <button type="button" onclick="openCreate()"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 cursor-pointer">
                + Nuevo registro
            </button>
        </header>

        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 sm:max-w-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total de registros</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900"><?php echo e($rows->count()); ?></p>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="flex flex-col gap-4 border-b border-gray-100 p-4 sm:p-5 md:flex-row md:items-center md:justify-between">
                <h2 class="text-[15px] font-bold text-slate-800">Listado de <?php echo e(mb_strtolower($cfg['title'])); ?></h2>
                <input type="text" id="catalogSearch" placeholder="Buscar..."
                    class="w-full md:max-w-sm rounded-lg border border-slate-200 bg-slate-50 py-2.5 px-4 text-[13px] text-slate-800 placeholder-slate-400 focus:bg-white focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100 transition">
            </div>

            <div class="overflow-x-auto">
                <table id="catalogTable" class="w-full min-w-[860px] text-left text-[13px] text-slate-600">
                    <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-6 py-3.5">ID</th>
                            <?php $__currentLoopData = $cfg['columns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-6 py-3.5"><?php echo e($column['label']); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-6 py-3.5 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="transition-colors hover:bg-gray-50/70">
                                <td class="px-6 py-4 font-mono text-[12px] font-bold text-slate-700"><?php echo e($row['id']); ?></td>
                                <?php $__currentLoopData = $row['cells']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="px-6 py-4"><?php echo e($cell); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <?php if($row['locked']): ?>
                                            <span class="rounded-md bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-500">Protegido</span>
                                        <?php else: ?>
                                            <button type="button" onclick="openEdit(<?php echo e($row['id']); ?>)" title="Editar"
                                                class="inline-flex h-8 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-[12px] font-semibold text-slate-700 shadow-sm hover:bg-slate-100 transition cursor-pointer">Editar</button>
                                            <form id="delete-<?php echo e($row['id']); ?>" action="<?php echo e($row['delete_url']); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" onclick="confirmDelete(<?php echo e($row['id']); ?>)" title="Eliminar"
                                                    class="inline-flex h-8 items-center justify-center rounded-lg border border-red-200 bg-white px-3 text-[12px] font-semibold text-red-600 shadow-sm hover:bg-red-50 transition cursor-pointer">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div id="catalogModal" class="hidden fixed inset-0 z-[60] items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <div class="w-full max-w-2xl max-h-[92vh] overflow-y-auto rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
                <h2 id="catalogModalTitle" class="font-bold"></h2>
                <button type="button" onclick="closeModal()" class="text-slate-300 hover:text-white cursor-pointer" aria-label="Cerrar">✕</button>
            </div>

            <form id="catalogForm" action="<?php echo e($storeUrl); ?>" method="POST" class="space-y-5 p-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="f-_method" value="POST" disabled>
                <input type="hidden" name="_mode" id="f-_mode" value="create">
                <input type="hidden" name="_id" id="f-_id" value="">

                <?php if($errors->any()): ?>
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[12.5px] text-red-800" role="alert">
                        <p class="font-bold mb-1">Revisa los siguientes campos:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="grid gap-4 sm:grid-cols-2">
                    <?php $__currentLoopData = $cfg['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="<?php echo e(! empty($field['full']) ? 'sm:col-span-2' : ''); ?>">
                            <label for="f-<?php echo e($field['name']); ?>" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                <?php echo e($field['label']); ?>

                                <?php if(in_array('required', $field['rules'], true)): ?> <span class="text-red-600">*</span> <?php endif; ?>
                            </label>

                            <?php if($field['type'] === 'select'): ?>
                                <select name="<?php echo e($field['name']); ?>" id="f-<?php echo e($field['name']); ?>" class="<?php echo e($inputClass); ?>">
                                    <option value="">— Seleccionar —</option>
                                    <?php $__currentLoopData = $selects[$field['name']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php elseif(in_array($field['type'], ['textarea', 'json'], true)): ?>
                                <textarea name="<?php echo e($field['name']); ?>" id="f-<?php echo e($field['name']); ?>" rows="<?php echo e($field['type'] === 'json' ? 5 : 3); ?>"
                                    class="<?php echo e($inputClass); ?> <?php echo e($field['type'] === 'json' ? 'font-mono' : ''); ?>"></textarea>
                            <?php else: ?>
                                <input type="<?php echo e($field['type']); ?>" name="<?php echo e($field['name']); ?>" id="f-<?php echo e($field['name']); ?>"
                                    <?php if(isset($field['min'])): ?> min="<?php echo e($field['min']); ?>" <?php endif; ?>
                                    <?php if(isset($field['max'])): ?> max="<?php echo e($field['max']); ?>" <?php endif; ?>
                                    <?php if(isset($field['step'])): ?> step="<?php echo e($field['step']); ?>" <?php endif; ?>
                                    <?php if(isset($field['maxlength'])): ?> maxlength="<?php echo e($field['maxlength']); ?>" <?php endif; ?>
                                    class="<?php echo e($inputClass); ?>">
                            <?php endif; ?>

                            <?php if(isset($field['help'])): ?>
                                <p class="mt-1 text-[11.5px] text-slate-500"><?php echo e($field['help']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeModal()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
                    <button type="submit"
                        class="rounded-lg bg-[#C8102E] px-4 py-2 text-sm font-semibold text-white hover:bg-[#9e0b22] cursor-pointer">Guardar</button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        const STORE_URL = <?php echo json_encode($storeUrl, 15, 512) ?>;
        const FIELDS = <?php echo json_encode(array_column($cfg['fields'], 'name'), 512) ?>;
        const ROWS = <?php echo json_encode($rowData, 15, 512) ?>;
        const SINGULAR = <?php echo json_encode($cfg['singular'], 15, 512) ?>;

        const table = $('#catalogTable').DataTable({
            dom: 'rtip',
            pageLength: 10,
            order: [[0, 'desc']],
            columnDefs: [{ targets: -1, orderable: false, searchable: false }],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                emptyTable: 'Aún no hay registros. Crea el primero con «Nuevo registro».',
            },
        });
        document.getElementById('catalogSearch').addEventListener('input', (e) => table.search(e.target.value).draw());

        const modal = document.getElementById('catalogModal');
        const form = document.getElementById('catalogForm');

        function fillForm(values) {
            FIELDS.forEach((name) => {
                const el = document.getElementById('f-' + name);
                if (el) el.value = values[name] ?? '';
            });
        }

        function showModal(title, mode, id, action, values) {
            form.action = action;
            document.getElementById('catalogModalTitle').textContent = title;
            document.getElementById('f-_mode').value = mode;
            document.getElementById('f-_id').value = id ?? '';
            document.getElementById('f-_method').disabled = mode !== 'edit';
            if (mode === 'edit') document.getElementById('f-_method').value = 'PUT';
            fillForm(values);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openCreate() {
            showModal('Nuevo registro (' + SINGULAR + ')', 'create', null, STORE_URL, {});
        }

        function openEdit(id) {
            const row = ROWS[id];
            showModal('Editar ' + SINGULAR + ' #' + id, 'edit', id, row.update_url, row.values);
        }

        function confirmDelete(id) {
            Swal.fire({
                title: '¿Eliminar registro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C8102E',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((r) => { if (r.isConfirmed) document.getElementById('delete-' + id).submit(); });
        }

        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

        // Si la validación falló, reabrir el modal con lo que escribió el usuario.
        <?php if($errors->any() && $oldMode): ?>
            (function () {
                const old = <?php echo json_encode(old(), 15, 512) ?>;
                const id = old._id || null;
                if (old._mode === 'edit' && id && ROWS[id]) {
                    showModal('Editar ' + SINGULAR + ' #' + id, 'edit', id, ROWS[id].update_url, old);
                } else {
                    showModal('Nuevo registro (' + SINGULAR + ')', 'create', null, STORE_URL, old);
                }
            })();
        <?php endif; ?>
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/catalogs/index.blade.php ENDPATH**/ ?>