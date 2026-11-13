<?php $__env->startSection('title', 'Mi práctica'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Mi práctica','section' => 'Área estudiante','description' => 'Sigue el avance de tu práctica, registra tu bitácora quincenal y evalúa a la empresa al terminar.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Mi práctica','section' => 'Área estudiante','description' => 'Sigue el avance de tu práctica, registra tu bitácora quincenal y evalúa a la empresa al terminar.']); ?>
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

        <?php $__empty_1 = true; $__currentLoopData = $practices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $practice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $offer = $practice->application?->offer;
                $company = $offer?->company;
                $stepIndex = array_search($practice->estado, $states, true);
                $canLog = in_array($practice->estado, $logStates, true);
                $canReview = in_array($practice->estado, $reviewStates, true);
                $modalId = 'logModal'.$practice->id;
                $reviewModalId = 'reviewModal'.$practice->id;
                $lastLog = $practice->logs->last();
                $suggestedStart = $lastLog ? $lastLog->period_end->copy()->addDay() : ($practice->fecha_inicio ?? today());
                $suggestedEnd = $suggestedStart->copy()->addDays($periodDays - 1)->min(today());
            ?>

            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="flex flex-col gap-3 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-red-700"><?php echo e($company?->razon_social ?? $offer?->empresa); ?></p>
                        <h2 class="text-lg font-extrabold text-slate-900"><?php echo e($offer?->titulo); ?></h2>
                        <p class="text-[12.5px] text-slate-500">
                            <?php echo e($practice->fecha_inicio?->format('d-m-Y') ?? 'Sin fecha de inicio'); ?> → <?php echo e($practice->fecha_termino?->format('d-m-Y') ?? 'sin fecha de término'); ?>

                            · Supervisor: <?php echo e($company?->contacto_nombre ?? '—'); ?>

                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?>
                        <?php if($practice->final_grade !== null): ?>
                            <span class="text-2xl font-extrabold <?php echo e($practice->isApproved() ? 'text-emerald-700' : 'text-red-700'); ?>"><?php echo e(number_format((float) $practice->final_grade, 1, ',', '')); ?></span>
                            <a href="<?php echo e(route('practices.acta', $practice)); ?>" class="rounded-lg bg-[#C8102E] px-3 py-2 text-[12.5px] font-semibold text-white hover:bg-[#9e0b22]">Descargar acta</a>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center">
                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex flex-col items-center gap-1 shrink-0">
                                <span class="w-3.5 h-3.5 rounded-full <?php echo e($index <= $stepIndex ? 'bg-red-700' : 'bg-slate-200'); ?>"></span>
                                <span class="text-[10.5px] font-semibold <?php echo e($index <= $stepIndex ? 'text-slate-700' : 'text-slate-400'); ?>"><?php echo e($state); ?></span>
                            </div>
                            <?php if(! $loop->last): ?>
                                <span class="h-0.5 flex-1 mb-4 <?php echo e($index < $stepIndex ? 'bg-red-700' : 'bg-slate-200'); ?>"></span>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="grid gap-0 lg:grid-cols-3">
                    
                    <div class="lg:col-span-2 p-5 lg:border-r border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-slate-800">Bitácora quincenal</h3>
                                <p class="text-[12px] text-slate-500"><?php echo e($practice->logs->count()); ?> registros · <?php echo e($practice->logs->sum('hours')); ?> horas</p>
                            </div>
                            <?php if($canLog): ?>
                                <button type="button" onclick="openModal('<?php echo e($modalId); ?>')" class="rounded-lg bg-slate-900 px-3 py-2 text-[12.5px] font-semibold text-white hover:bg-slate-800 cursor-pointer">+ Registrar avance</button>
                            <?php endif; ?>
                        </div>

                        <?php if(! $canLog && $practice->logs->isEmpty()): ?>
                            <p class="text-[12.5px] text-slate-500">La bitácora se habilita cuando tu práctica esté «En Curso».</p>
                        <?php endif; ?>

                        <div class="space-y-3">
                            <?php $__currentLoopData = $practice->logs->sortByDesc('period_start'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-slate-800 text-[13px]"><?php echo e($log->period_start->format('d-m-Y')); ?> → <?php echo e($log->period_end->format('d-m-Y')); ?> · <?php echo e($log->hours); ?> h</p>
                                        <?php if($canLog): ?>
                                            <form id="delete-log-<?php echo e($log->id); ?>" action="<?php echo e(route('student.practice.logs.destroy', $log)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'delete','title' => 'Eliminar registro','onclick' => 'confirmSubmit(\'delete-log-'.e($log->id).'\', { title: \'¿Eliminar este registro?\', text: \'Se borrará de tu bitácora.\', confirmText: \'Sí, eliminar\' })']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'delete','title' => 'Eliminar registro','onclick' => 'confirmSubmit(\'delete-log-'.e($log->id).'\', { title: \'¿Eliminar este registro?\', text: \'Se borrará de tu bitácora.\', confirmText: \'Sí, eliminar\' })']); ?>
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
                                    <p class="mt-1 text-[13px] text-slate-700 whitespace-pre-line"><?php echo e($log->activities); ?></p>
                                    <?php if($log->learnings): ?><p class="mt-1 text-[12px] text-slate-600"><b>Aprendizajes:</b> <?php echo e($log->learnings); ?></p><?php endif; ?>
                                    <?php if($log->difficulties): ?><p class="mt-1 text-[12px] text-slate-600"><b>Dificultades:</b> <?php echo e($log->difficulties); ?></p><?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div class="p-5 border-t lg:border-t-0 border-gray-100">
                        <h3 class="font-bold text-slate-800">Evalúa a la empresa</h3>
                        <?php if($practice->review): ?>
                            <p class="mt-2 text-2xl text-amber-500"><?php echo e(str_repeat('★', $practice->review->rating)); ?><span class="text-slate-300"><?php echo e(str_repeat('★', 5 - $practice->review->rating)); ?></span></p>
                            <?php if($practice->review->comment): ?>
                                <p class="mt-1 text-[12.5px] text-slate-600 whitespace-pre-line"><?php echo e($practice->review->comment); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if($canReview): ?>
                            <button type="button" onclick="openModal('<?php echo e($reviewModalId); ?>')" class="mt-3 rounded-lg border border-slate-300 px-3 py-2 text-[12.5px] font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                                <?php echo e($practice->review ? 'Cambiar mi evaluación' : 'Evaluar empresa'); ?>

                            </button>
                        <?php elseif(! $practice->review): ?>
                            <p class="mt-2 text-[12.5px] text-slate-500">Podrás evaluar a la empresa cuando tu práctica esté evaluada. Tu opinión ayuda a otros estudiantes.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if($canLog): ?>
                <?php if (isset($component)) { $__componentOriginaldf85e6552b95df616d59caebcbf2473d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf85e6552b95df616d59caebcbf2473d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-modal','data' => ['id' => $modalId,'title' => 'Registrar avance quincenal','subtitle' => 'Bitácora de «'.$offer?->titulo.'»']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalId),'title' => 'Registrar avance quincenal','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Bitácora de «'.$offer?->titulo.'»')]); ?>
                    <form action="<?php echo e(route('student.practice.logs.store', $practice)); ?>" method="POST" class="space-y-4 p-6">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_modal" value="<?php echo e($modalId); ?>">
                        <?php if (isset($component)) { $__componentOriginal3d251db05a714ec6b2f220d3dccfef6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-errors','data' => ['modal' => $modalId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalId)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d)): ?>
<?php $attributes = $__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d; ?>
<?php unset($__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3d251db05a714ec6b2f220d3dccfef6d)): ?>
<?php $component = $__componentOriginal3d251db05a714ec6b2f220d3dccfef6d; ?>
<?php unset($__componentOriginal3d251db05a714ec6b2f220d3dccfef6d); ?>
<?php endif; ?>
                        <?php $old = fn (string $key, $default = null) => old('_modal') === $modalId ? old($key, $default) : $default; ?>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Desde','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Desde','required' => true]); ?>
                                <input type="date" name="period_start" required value="<?php echo e($old('period_start', $suggestedStart->toDateString())); ?>" class="form-input">
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Hasta','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Hasta','required' => true]); ?>
                                <input type="date" name="period_end" required value="<?php echo e($old('period_end', $suggestedEnd->toDateString())); ?>" class="form-input">
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Horas trabajadas','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Horas trabajadas','required' => true]); ?>
                                <input type="number" name="hours" required min="1" max="120" value="<?php echo e($old('hours', 40)); ?>" class="form-input">
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Actividades realizadas','required' => true,'hint' => 'Mínimo 20 caracteres.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Actividades realizadas','required' => true,'hint' => 'Mínimo 20 caracteres.']); ?>
                            <textarea name="activities" rows="4" required minlength="20" maxlength="3000" class="form-input"><?php echo e($old('activities')); ?></textarea>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Aprendizajes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Aprendizajes']); ?>
                            <textarea name="learnings" rows="2" maxlength="3000" class="form-input"><?php echo e($old('learnings')); ?></textarea>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Dificultades']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Dificultades']); ?>
                            <textarea name="difficulties" rows="2" maxlength="3000" class="form-input"><?php echo e($old('difficulties')); ?></textarea>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal98d29adaa4406bec3dfa33b112d9a220 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98d29adaa4406bec3dfa33b112d9a220 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-actions','data' => ['modal' => $modalId,'label' => 'Guardar avance']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalId),'label' => 'Guardar avance']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal98d29adaa4406bec3dfa33b112d9a220)): ?>
<?php $attributes = $__attributesOriginal98d29adaa4406bec3dfa33b112d9a220; ?>
<?php unset($__attributesOriginal98d29adaa4406bec3dfa33b112d9a220); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal98d29adaa4406bec3dfa33b112d9a220)): ?>
<?php $component = $__componentOriginal98d29adaa4406bec3dfa33b112d9a220; ?>
<?php unset($__componentOriginal98d29adaa4406bec3dfa33b112d9a220); ?>
<?php endif; ?>
                    </form>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf85e6552b95df616d59caebcbf2473d)): ?>
<?php $attributes = $__attributesOriginaldf85e6552b95df616d59caebcbf2473d; ?>
<?php unset($__attributesOriginaldf85e6552b95df616d59caebcbf2473d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf85e6552b95df616d59caebcbf2473d)): ?>
<?php $component = $__componentOriginaldf85e6552b95df616d59caebcbf2473d; ?>
<?php unset($__componentOriginaldf85e6552b95df616d59caebcbf2473d); ?>
<?php endif; ?>
            <?php endif; ?>

            <?php if($canReview): ?>
                <?php if (isset($component)) { $__componentOriginaldf85e6552b95df616d59caebcbf2473d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf85e6552b95df616d59caebcbf2473d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-modal','data' => ['id' => $reviewModalId,'title' => 'Evaluar a la empresa','subtitle' => $company?->razon_social]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reviewModalId),'title' => 'Evaluar a la empresa','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($company?->razon_social)]); ?>
                    <form action="<?php echo e(route('student.practice.review.store', $practice)); ?>" method="POST" class="space-y-4 p-6">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_modal" value="<?php echo e($reviewModalId); ?>">
                        <?php if (isset($component)) { $__componentOriginal3d251db05a714ec6b2f220d3dccfef6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-errors','data' => ['modal' => $reviewModalId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reviewModalId)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d)): ?>
<?php $attributes = $__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d; ?>
<?php unset($__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3d251db05a714ec6b2f220d3dccfef6d)): ?>
<?php $component = $__componentOriginal3d251db05a714ec6b2f220d3dccfef6d; ?>
<?php unset($__componentOriginal3d251db05a714ec6b2f220d3dccfef6d); ?>
<?php endif; ?>
                        <?php $currentRating = (int) (old('_modal') === $reviewModalId ? old('rating') : $practice->review?->rating); ?>
                        <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => '¿Cómo fue tu experiencia en la empresa?','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '¿Cómo fue tu experiencia en la empresa?','required' => true]); ?>
                            <div class="flex flex-row-reverse justify-end gap-1 text-3xl star-rating">
                                <?php for($star = 5; $star >= 1; $star--): ?>
                                    <input type="radio" id="rating-<?php echo e($practice->id); ?>-<?php echo e($star); ?>" name="rating" value="<?php echo e($star); ?>" class="peer sr-only" <?php if($currentRating === $star): echo 'checked'; endif; ?> required>
                                    <label for="rating-<?php echo e($practice->id); ?>-<?php echo e($star); ?>" class="cursor-pointer text-slate-300" title="<?php echo e($star); ?> de 5">★</label>
                                <?php endfor; ?>
                            </div>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Comentario (opcional)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Comentario (opcional)']); ?>
                            <textarea name="comment" rows="3" maxlength="2000" class="form-input"><?php echo e(old('_modal') === $reviewModalId ? old('comment') : $practice->review?->comment); ?></textarea>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal98d29adaa4406bec3dfa33b112d9a220 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98d29adaa4406bec3dfa33b112d9a220 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-actions','data' => ['modal' => $reviewModalId,'label' => 'Enviar evaluación']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reviewModalId),'label' => 'Enviar evaluación']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal98d29adaa4406bec3dfa33b112d9a220)): ?>
<?php $attributes = $__attributesOriginal98d29adaa4406bec3dfa33b112d9a220; ?>
<?php unset($__attributesOriginal98d29adaa4406bec3dfa33b112d9a220); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal98d29adaa4406bec3dfa33b112d9a220)): ?>
<?php $component = $__componentOriginal98d29adaa4406bec3dfa33b112d9a220; ?>
<?php unset($__componentOriginal98d29adaa4406bec3dfa33b112d9a220); ?>
<?php endif; ?>
                    </form>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf85e6552b95df616d59caebcbf2473d)): ?>
<?php $attributes = $__attributesOriginaldf85e6552b95df616d59caebcbf2473d; ?>
<?php unset($__attributesOriginaldf85e6552b95df616d59caebcbf2473d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf85e6552b95df616d59caebcbf2473d)): ?>
<?php $component = $__componentOriginaldf85e6552b95df616d59caebcbf2473d; ?>
<?php unset($__componentOriginaldf85e6552b95df616d59caebcbf2473d); ?>
<?php endif; ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-xl bg-white p-10 text-center shadow-sm ring-1 ring-gray-200">
                <p class="text-lg font-bold text-slate-800">Aún no tienes una práctica asignada</p>
                <p class="mt-1 text-sm text-slate-500">Cuando el coordinador te asigne una práctica, aparecerá aquí.</p>
                <a href="<?php echo e(route('student.offers.view')); ?>" class="mt-4 inline-flex rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Explorar ofertas</a>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <style>
        /* Estrellas: al pasar el mouse o elegir una, se pintan esa y las anteriores */
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label { color: #f59e0b; }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/student/practice.blade.php ENDPATH**/ ?>