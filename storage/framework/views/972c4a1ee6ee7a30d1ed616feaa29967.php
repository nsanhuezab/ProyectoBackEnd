<?php
    $application = $practice->application;
    $student = $application?->student;
    $studentUser = $student?->user;
    $studentName = trim(($studentUser?->name ?? '').' '.($studentUser?->last_name ?? '')) ?: 'Estudiante';
    $offer = $application?->offer;
    $company = $offer?->company;
    $career = $student?->career;
    $stepIndex = array_search($practice->estado, $states, true);
    $nextState = $stepIndex === false ? null : ($states[$stepIndex + 1] ?? null);
    $supervisorEvaluation = $practice->evaluationBy(\App\Models\PracticeEvaluation::SUPERVISOR);
    $coordinatorEvaluation = $practice->evaluationBy(\App\Models\PracticeEvaluation::COORDINATOR);
    $approved = $practice->isApproved();
    $totalHours = $practice->logs->sum('hours');
    $grade = fn ($value) => $value === null ? '—' : number_format((float) $value, 1, ',', '');
?>

<?php $__env->startSection('title', 'Práctica de '.$studentName); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Práctica de '.$studentName,'description' => ($offer?->titulo ?? 'Oferta').' · '.($company?->razon_social ?? $offer?->empresa),'section' => 'Prácticas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Práctica de '.$studentName),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($offer?->titulo ?? 'Oferta').' · '.($company?->razon_social ?? $offer?->empresa)),'section' => 'Prácticas']); ?>
             <?php $__env->slot('actions', null, []); ?> 
                <?php if($can['manage']): ?>
                    <a href="<?php echo e(route('practices.index')); ?>" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Volver a prácticas</a>
                <?php endif; ?>
                <?php if($practice->final_grade !== null): ?>
                    <a href="<?php echo e(route('practices.acta', $practice)); ?>" class="inline-flex items-center gap-2 rounded-lg bg-[#C8102E] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-[#9e0b22]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Acta de cierre (PDF)
                    </a>
                <?php endif; ?>
             <?php $__env->endSlot(); ?>
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

        
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <ol class="flex flex-1 items-center min-w-[320px]">
                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $done = $index <= $stepIndex; ?>
                        <li class="flex items-center <?php echo e($loop->last ? '' : 'flex-1'); ?>">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold <?php echo e($done ? 'bg-red-700 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200'); ?>"><?php echo e($index + 1); ?></span>
                                <span class="text-[11px] font-semibold whitespace-nowrap <?php echo e($done ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($state); ?></span>
                            </div>
                            <?php if(! $loop->last): ?>
                                <span class="h-0.5 flex-1 mx-2 mb-5 <?php echo e($index < $stepIndex ? 'bg-red-700' : 'bg-slate-200'); ?>"></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>

                <?php if($can['manage'] && $nextState): ?>
                    <form id="transition-practice" action="<?php echo e(route('practices.transition', $practice)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="estado" value="<?php echo e($nextState); ?>">
                        <button type="button"
                            onclick="confirmSubmit('transition-practice', { title: <?php echo e(json_encode('¿Pasar a «'.$nextState.'»?')); ?>, text: <?php echo e(json_encode($nextState === 'Evaluado' ? 'Se calculará la nota final con las dos rúbricas.' : 'Se avisará al estudiante.')); ?>, confirmText: 'Sí, avanzar', color: '#0369a1', icon: 'question' })"
                            class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 cursor-pointer">
                            Pasar a «<?php echo e($nextState); ?>» →
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            
            <div class="lg:col-span-2 rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-[15px] font-bold text-slate-800">Datos de la práctica</h2>
                </div>
                <dl class="grid gap-x-6 gap-y-4 p-5 sm:grid-cols-2 text-[13px]">
                    <div><dt class="text-[11.5px] text-slate-500">Estudiante</dt><dd class="font-semibold text-slate-800"><?php echo e($studentName); ?></dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Carrera</dt><dd class="font-semibold text-slate-800"><?php echo e($career?->nombre ?? '—'); ?></dd></div>
                    <?php if($can['seeStudentContact']): ?>
                        <div><dt class="text-[11.5px] text-slate-500">Correo del estudiante</dt><dd class="font-semibold text-slate-800"><?php echo e($studentUser?->email ?? '—'); ?></dd></div>
                    <?php endif; ?>
                    <div><dt class="text-[11.5px] text-slate-500">Nivel / año</dt><dd class="font-semibold text-slate-800"><?php echo e($student?->nivel_avance ?? '—'); ?><?php echo e($student?->year_level ? ' · '.$student->year_level.'° año' : ''); ?></dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Empresa</dt><dd class="font-semibold text-slate-800"><?php echo e($company?->razon_social ?? $offer?->empresa); ?></dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Supervisor de práctica</dt><dd class="font-semibold text-slate-800"><?php echo e($company?->contacto_nombre ?? '—'); ?></dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Cargo / oferta</dt><dd class="font-semibold text-slate-800"><?php echo e($offer?->titulo); ?></dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Modalidad</dt><dd class="font-semibold text-slate-800"><?php echo e($offer?->modalidad ?? '—'); ?></dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Período</dt><dd class="font-semibold text-slate-800"><?php echo e($practice->fecha_inicio?->format('d-m-Y') ?? '—'); ?> → <?php echo e($practice->fecha_termino?->format('d-m-Y') ?? '—'); ?></dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Horas registradas en bitácora</dt><dd class="font-semibold text-slate-800"><?php echo e($totalHours); ?> h (<?php echo e($practice->logs->count()); ?> registros)</dd></div>
                    <div>
                        <dt class="text-[11.5px] text-slate-500">Contratado por la empresa</dt>
                        <dd class="font-semibold text-slate-800"><?php echo e($practice->hired === null ? 'Sin información' : ($practice->hired ? 'Sí' : 'No')); ?></dd>
                    </div>
                </dl>
            </div>

            
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-5 flex flex-col">
                <h2 class="text-[15px] font-bold text-slate-800">Nota final</h2>
                <p class="text-[12px] text-slate-500">Supervisor <?php echo e(rtrim(rtrim(number_format((float) $career?->supervisor, 2, ',', ''), '0'), ',')); ?>% · Coordinador <?php echo e(rtrim(rtrim(number_format((float) $career?->coordinador, 2, ',', ''), '0'), ',')); ?>%</p>
                <div class="flex-1 flex flex-col items-center justify-center py-6">
                    <p class="text-5xl font-extrabold <?php echo e($approved === null ? 'text-slate-300' : ($approved ? 'text-emerald-700' : 'text-red-700')); ?>"><?php echo e($grade($practice->final_grade)); ?></p>
                    <?php if($approved !== null): ?>
                        <span class="mt-2 inline-flex px-3 py-1 rounded-full text-[12px] font-bold <?php echo e($approved ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'); ?>">
                            <?php echo e($approved ? 'Aprobada' : 'Reprobada'); ?>

                        </span>
                    <?php else: ?>
                        <p class="mt-2 text-[12px] text-slate-500 text-center">Se calcula cuando están las dos rúbricas.</p>
                    <?php endif; ?>
                </div>
                <div class="grid grid-cols-2 gap-2 text-center text-[12px]">
                    <div class="rounded-lg bg-slate-50 p-2"><p class="text-slate-500">Supervisor</p><p class="font-bold text-slate-800"><?php echo e($grade($supervisorEvaluation?->grade)); ?></p></div>
                    <div class="rounded-lg bg-slate-50 p-2"><p class="text-slate-500">Coordinador</p><p class="font-bold text-slate-800"><?php echo e($grade($coordinatorEvaluation?->grade)); ?></p></div>
                </div>
            </div>
        </div>

        
        <div class="grid gap-6 lg:grid-cols-2">
            <?php
                $rubrics = [
                    \App\Models\PracticeEvaluation::SUPERVISOR => ['Evaluación del supervisor', $supervisorEvaluation, $can['evaluateSupervisor']],
                    \App\Models\PracticeEvaluation::COORDINATOR => ['Evaluación del coordinador', $coordinatorEvaluation, $can['evaluateCoordinator']],
                ];
            ?>
            <?php $__currentLoopData = $rubrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => [$title, $evaluation, $canEvaluate]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                        <div>
                            <h2 class="text-[15px] font-bold text-slate-800"><?php echo e($title); ?></h2>
                            <p class="text-[12px] text-slate-500">
                                <?php if($evaluation): ?>
                                    <?php echo e($evaluation->evaluator ? trim($evaluation->evaluator->name.' '.$evaluation->evaluator->last_name) : 'Registrada'); ?> · <?php echo e($evaluation->updated_at?->format('d-m-Y')); ?>

                                <?php else: ?>
                                    Pendiente
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php if($canEvaluate): ?>
                            <button type="button" onclick="openModal('<?php echo e($role); ?>EvaluationModal')"
                                class="rounded-lg bg-slate-900 px-3 py-2 text-[12.5px] font-semibold text-white hover:bg-slate-800 cursor-pointer">
                                <?php echo e($evaluation ? 'Editar rúbrica' : 'Completar rúbrica'); ?>

                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if($evaluation): ?>
                        <table class="w-full text-[13px]">
                            <tbody class="divide-y divide-gray-100">
                                <?php $__currentLoopData = $competencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-5 py-2.5 text-slate-600"><?php echo e($label); ?></td>
                                        <td class="px-5 py-2.5 text-right font-bold text-slate-800"><?php echo e($grade($evaluation->scores[$key] ?? null)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="bg-slate-50">
                                    <td class="px-5 py-2.5 font-bold text-slate-800">Promedio</td>
                                    <td class="px-5 py-2.5 text-right font-extrabold text-red-700"><?php echo e($grade($evaluation->grade)); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php if($evaluation->comments): ?>
                            <p class="px-5 py-4 text-[12.5px] text-slate-600 whitespace-pre-line border-t border-gray-100"><?php echo e($evaluation->comments); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="px-5 py-8 text-center text-[13px] text-slate-500">
                            <?php if(! in_array($practice->estado, ['Informe Entregado', 'Evaluado', 'Finalizada'], true)): ?>
                                Se completa al cierre, cuando la práctica esté en «Informe Entregado».
                            <?php else: ?>
                                Aún no se completa esta rúbrica.
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if($canEvaluate): ?>
                    <?php $modalId = $role.'EvaluationModal'; $oldScores = old('_modal') === $modalId ? old('scores', []) : []; ?>
                    <?php if (isset($component)) { $__componentOriginaldf85e6552b95df616d59caebcbf2473d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf85e6552b95df616d59caebcbf2473d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-modal','data' => ['id' => $modalId,'title' => $title,'subtitle' => 'Califica cada competencia de 1,0 a 7,0.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalId),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'subtitle' => 'Califica cada competencia de 1,0 a 7,0.']); ?>
                        <form action="<?php echo e(route('practices.evaluations.store', $practice)); ?>" method="POST" class="space-y-5 p-6">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="_modal" value="<?php echo e($modalId); ?>">
                            <input type="hidden" name="evaluator_role" value="<?php echo e($role); ?>">
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

                            <div class="divide-y divide-slate-100 rounded-lg border border-slate-200">
                                <?php $__currentLoopData = $competencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center justify-between gap-4 px-4 py-2.5">
                                        <span class="text-[13px] text-slate-700"><?php echo e($label); ?></span>
                                        <input type="number" name="scores[<?php echo e($key); ?>]" required min="1" max="7" step="0.1"
                                            value="<?php echo e($oldScores[$key] ?? ($evaluation->scores[$key] ?? '')); ?>"
                                            class="form-input w-24 text-center font-bold">
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Comentarios','for' => ''.e($role).'-comments']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Comentarios','for' => ''.e($role).'-comments']); ?>
                                <textarea id="<?php echo e($role); ?>-comments" name="comments" rows="3" maxlength="2000" class="form-input"><?php echo e(old('_modal') === $modalId ? old('comments') : $evaluation?->comments); ?></textarea>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-actions','data' => ['modal' => $modalId,'label' => 'Guardar evaluación']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalId),'label' => 'Guardar evaluación']); ?>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800">Bitácora quincenal</h2>
                    <p class="text-[12px] text-slate-500">La registra el estudiante desde «Mi práctica». Total: <?php echo e($totalHours); ?> horas.</p>
                </div>
            </div>
            <?php $__empty_1 = true; $__currentLoopData = $practice->logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-5 py-4 border-b border-gray-100 last:border-0">
                    <div class="flex items-center justify-between gap-4">
                        <p class="font-bold text-slate-800 text-[13px]"><?php echo e($log->period_start->format('d-m-Y')); ?> → <?php echo e($log->period_end->format('d-m-Y')); ?></p>
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11.5px] font-bold text-slate-600"><?php echo e($log->hours); ?> h</span>
                    </div>
                    <p class="mt-1.5 text-[13px] text-slate-700 whitespace-pre-line"><?php echo e($log->activities); ?></p>
                    <?php if($log->learnings): ?>
                        <p class="mt-1.5 text-[12.5px] text-slate-600"><span class="font-semibold">Aprendizajes:</span> <?php echo e($log->learnings); ?></p>
                    <?php endif; ?>
                    <?php if($log->difficulties): ?>
                        <p class="mt-1 text-[12.5px] text-slate-600"><span class="font-semibold">Dificultades:</span> <?php echo e($log->difficulties); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="px-5 py-8 text-center text-[13px] text-slate-500">Todavía no hay registros en la bitácora.</p>
            <?php endif; ?>
        </div>

        
        <?php if($practice->review): ?>
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 px-5 py-4">
                <h2 class="text-[15px] font-bold text-slate-800">Evaluación de la empresa hecha por el estudiante</h2>
                <p class="mt-1 text-xl text-amber-500" aria-label="<?php echo e($practice->review->rating); ?> de 5">
                    <?php echo e(str_repeat('★', $practice->review->rating)); ?><span class="text-slate-300"><?php echo e(str_repeat('★', 5 - $practice->review->rating)); ?></span>
                </p>
                <?php if($practice->review->comment): ?>
                    <p class="mt-1 text-[13px] text-slate-600 whitespace-pre-line"><?php echo e($practice->review->comment); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/practices/show.blade.php ENDPATH**/ ?>