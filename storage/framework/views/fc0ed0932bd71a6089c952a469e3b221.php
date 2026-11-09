<?php $__env->startSection('title', 'Practicantes'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Practicantes en mi empresa','section' => 'Área empresa','description' => 'Al cierre de cada práctica, el supervisor completa la rúbrica de evaluación de desempeño.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Practicantes en mi empresa','section' => 'Área empresa','description' => 'Al cierre de cada práctica, el supervisor completa la rúbrica de evaluación de desempeño.']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-card','data' => ['title' => 'Prácticas','total' => $practices->count(),'searchId' => 'companyPracticeSearch','searchPlaceholder' => 'Buscar por estudiante, oferta o estado...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Prácticas','total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($practices->count()),'search-id' => 'companyPracticeSearch','search-placeholder' => 'Buscar por estudiante, oferta o estado...']); ?>
             <?php $__env->slot('icon', null, []); ?> 
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
             <?php $__env->endSlot(); ?>

            <table id="companyPracticesTable" class="w-full min-w-[860px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Practicante</th>
                        <th class="px-6 py-3.5">Oferta</th>
                        <th class="px-6 py-3.5">Período</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5 text-center">Rúbrica del supervisor</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $practices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $practice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $user = $practice->application?->student?->user;
                            $evaluation = $practice->evaluations->firstWhere('evaluator_role', \App\Models\PracticeEvaluation::SUPERVISOR);
                            $pending = ! $evaluation && in_array($practice->estado, ['Informe Entregado', 'Evaluado'], true);
                        ?>
                        <tr class="hover:bg-gray-50/70">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800"><?php echo e(trim(($user?->name ?? '').' '.($user?->last_name ?? ''))); ?></p>
                                <p class="text-[12px] text-slate-500"><?php echo e($practice->application?->student?->career?->nombre); ?></p>
                            </td>
                            <td class="px-6 py-4"><?php echo e($practice->application?->offer?->titulo); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e($practice->fecha_inicio?->format('d-m-Y') ?? '—'); ?> → <?php echo e($practice->fecha_termino?->format('d-m-Y') ?? '—'); ?></td>
                            <td class="px-6 py-4 text-center"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $practice->estado]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($practice->estado)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if($evaluation): ?>
                                    <span class="font-bold text-emerald-700"><?php echo e(number_format((float) $evaluation->grade, 1, ',', '')); ?></span>
                                <?php elseif($pending): ?>
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pendiente</span>
                                <?php else: ?>
                                    <span class="text-slate-400 text-[12px]">Al cierre</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?php echo e(route('practices.show', $practice)); ?>" class="rounded-lg <?php echo e($pending ? 'bg-slate-900 text-white hover:bg-slate-800' : 'border border-slate-300 text-slate-700 hover:bg-slate-50'); ?> px-3 py-1.5 text-[12.5px] font-semibold">
                                    <?php echo e($pending ? 'Evaluar' : 'Ver'); ?>

                                </a>
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
        initDataTable('#companyPracticesTable', '#companyPracticeSearch', { order: [] });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/company/practices.blade.php ENDPATH**/ ?>