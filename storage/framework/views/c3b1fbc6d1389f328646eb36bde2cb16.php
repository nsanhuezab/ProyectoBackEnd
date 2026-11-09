<?php $__env->startSection('title', 'Mis postulaciones'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Mis postulaciones','section' => 'Área estudiante','description' => 'Revisa el estado de cada postulación, descarga tu CV y conversa con la coordinación.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Mis postulaciones','section' => 'Área estudiante','description' => 'Revisa el estado de cada postulación, descarga tu CV y conversa con la coordinación.']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-card','data' => ['title' => 'Postulaciones enviadas','total' => $applications->count(),'searchId' => 'myApplicationSearch','searchPlaceholder' => 'Buscar por oferta, empresa o estado...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Postulaciones enviadas','total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($applications->count()),'search-id' => 'myApplicationSearch','search-placeholder' => 'Buscar por oferta, empresa o estado...']); ?>
             <?php $__env->slot('icon', null, []); ?> 
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
             <?php $__env->endSlot(); ?>

            <table id="myApplicationsTable" class="w-full min-w-[820px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">Oferta</th>
                        <th class="px-6 py-3.5">Afinidad</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5">Fecha</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $score = (float) $application->matching_score;
                            $scoreColor = $score >= 70 ? 'bg-emerald-500' : ($score >= 40 ? 'bg-amber-500' : 'bg-red-600');
                            $messages = $application->messages->count();
                        ?>
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-[13.5px]"><?php echo e($application->offer?->titulo); ?></p>
                                <p class="text-[12px] text-slate-500"><?php echo e($application->offer?->company?->razon_social ?? $application->offer?->empresa); ?><?php echo e($application->offer?->modalidad ? ' · '.$application->offer->modalidad : ''); ?></p>
                            </td>
                            <td class="px-6 py-4" data-order="<?php echo e($score); ?>">
                                <div class="flex items-center gap-2.5 w-36">
                                    <div class="h-2 flex-1 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full <?php echo e($scoreColor); ?>" style="width: <?php echo e(min($score, 100)); ?>%"></div>
                                    </div>
                                    <span class="text-[12px] font-bold text-slate-700 w-10 text-right"><?php echo e(round($score)); ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $application->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($application->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" data-order="<?php echo e($application->created_at?->timestamp); ?>">
                                <?php echo e($application->created_at?->format('d-m-Y')); ?>

                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    
                                    <a href="<?php echo e(route('applications.show', $application)); ?>" title="Ver detalle y mensajes"
                                        class="relative inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                        <?php if($messages > 0): ?>
                                            <span class="absolute -top-1.5 -right-1.5 min-w-[16px] h-4 px-1 rounded-full bg-red-700 text-[9.5px] font-bold text-white flex items-center justify-center"><?php echo e($messages); ?></span>
                                        <?php endif; ?>
                                    </a>

                                    
                                    <?php if($application->cv_path): ?>
                                        <a href="<?php echo e(route('applications.cv', $application)); ?>" title="Descargar mi CV"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 shadow-sm hover:bg-red-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                    <?php endif; ?>

                                    
                                    <?php if($application->status === 'Postulado'): ?>
                                        <form id="withdraw-application-<?php echo e($application->id); ?>" action="<?php echo e(route('student.applications.destroy', $application)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'delete','title' => 'Retirar postulación','onclick' => 'confirmSubmit(\'withdraw-application-'.e($application->id).'\', { title: \'¿Retirar postulación?\', text: '.e(json_encode('Se retirará tu postulación a «'.$application->offer?->titulo.'» y se borrará el CV adjunto.')).', confirmText: \'Sí, retirar\' })']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'delete','title' => 'Retirar postulación','onclick' => 'confirmSubmit(\'withdraw-application-'.e($application->id).'\', { title: \'¿Retirar postulación?\', text: '.e(json_encode('Se retirará tu postulación a «'.$application->offer?->titulo.'» y se borrará el CV adjunto.')).', confirmText: \'Sí, retirar\' })']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                </div>
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
        initDataTable('#myApplicationsTable', '#myApplicationSearch', {
            order: [[3, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                emptyTable: 'Aún no has postulado a ninguna oferta. Revisa «Explorar ofertas».',
            },
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/student/applications.blade.php ENDPATH**/ ?>