<?php $__env->startSection('title', 'Historial de cambios'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Historial de cambios','description' => 'Registro de quién creó, modificó o eliminó información en la plataforma. No se puede editar.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial de cambios','description' => 'Registro de quién creó, modificó o eliminó información en la plataforma. No se puede editar.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal00be98f2cda19e6dc66afc21b5371ed8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal00be98f2cda19e6dc66afc21b5371ed8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-card','data' => ['title' => 'Últimos movimientos','total' => $logs->count(),'searchId' => 'activitySearch','searchPlaceholder' => 'Buscar por usuario, módulo o registro...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Últimos movimientos','total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($logs->count()),'search-id' => 'activitySearch','search-placeholder' => 'Buscar por usuario, módulo o registro...']); ?>
             <?php $__env->slot('icon', null, []); ?> 
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
             <?php $__env->endSlot(); ?>

            <table id="activityTable" class="w-full min-w-[960px] text-left text-[13px] text-slate-600">
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
                    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
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
                        ?>
                        <tr class="align-top hover:bg-gray-50/70">
                            <td class="px-6 py-3 whitespace-nowrap" data-order="<?php echo e($log->created_at?->timestamp); ?>"><?php echo e($log->created_at?->format('d-m-Y H:i')); ?></td>
                            <td class="px-6 py-3">
                                <p class="font-semibold text-slate-800"><?php echo e($log->user ? trim($log->user->name.' '.$log->user->last_name) : 'Sistema'); ?></p>
                                <p class="text-[11.5px] text-slate-500"><?php echo e($log->user?->email); ?></p>
                            </td>
                            <td class="px-6 py-3"><span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold border <?php echo e($actionStyle); ?>"><?php echo e($log->actionName()); ?></span></td>
                            <td class="px-6 py-3 font-medium text-slate-700"><?php echo e($log->moduleName()); ?></td>
                            <td class="px-6 py-3"><?php echo e($log->description); ?></td>
                            <td class="px-6 py-3">
                                <?php if($changes->isNotEmpty()): ?>
                                    <details>
                                        <summary class="cursor-pointer text-[12px] font-semibold text-red-700"><?php echo e($changes->count()); ?> campo(s)</summary>
                                        <ul class="mt-1 space-y-0.5 text-[11.5px] text-slate-600 max-w-[360px] break-words">
                                            <?php $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e(\Illuminate\Support\Str::limit($line, 160)); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </details>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal00be98f2cda19e6dc66afc21b5371ed8)): ?>
<?php $attributes = $__attributesOriginal00be98f2cda19e6dc66afc21b5371ed8; ?>
<?php unset($__attributesOriginal00be98f2cda19e6dc66afc21b5371ed8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal00be98f2cda19e6dc66afc21b5371ed8)): ?>
<?php $component = $__componentOriginal00be98f2cda19e6dc66afc21b5371ed8; ?>
<?php unset($__componentOriginal00be98f2cda19e6dc66afc21b5371ed8); ?>
<?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        initDataTable('#activityTable', '#activitySearch', { order: [[0, 'desc']], pageLength: 25 });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/activity/index.blade.php ENDPATH**/ ?>