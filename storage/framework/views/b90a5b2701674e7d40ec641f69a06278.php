<?php
    $studentUser = $application->student?->user;
    $studentName = trim(($studentUser?->name ?? '').' '.($studentUser?->last_name ?? '')) ?: 'Estudiante';
    $offer = $application->offer;
    $score = (float) $application->matching_score;
?>

<?php $__env->startSection('title', 'Postulación de '.$studentName); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $isStudent ? 'Mi postulación' : 'Postulación de '.$studentName,'section' => $isStudent ? 'Área estudiante' : 'Postulaciones','description' => ($offer?->titulo ?? '').' · '.($offer?->company?->razon_social ?? $offer?->empresa)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isStudent ? 'Mi postulación' : 'Postulación de '.$studentName),'section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isStudent ? 'Área estudiante' : 'Postulaciones'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($offer?->titulo ?? '').' · '.($offer?->company?->razon_social ?? $offer?->empresa))]); ?>
             <?php $__env->slot('actions', null, []); ?> 
                <a href="<?php echo e($isStudent ? route('student.applications.view') : route('applications.index')); ?>"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Volver</a>
                <?php if($application->practice && ! $isStudent): ?>
                    <a href="<?php echo e(route('practices.show', $application->practice)); ?>" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Ver práctica</a>
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

        <div class="grid gap-6 lg:grid-cols-3">
            
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-5 space-y-4 text-[13px]">
                <div class="flex items-center justify-between">
                    <h2 class="text-[15px] font-bold text-slate-800">Resumen</h2>
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
                </div>
                <dl class="space-y-3">
                    <div><dt class="text-[11.5px] text-slate-500">Estudiante</dt><dd class="font-semibold text-slate-800"><?php echo e($studentName); ?> · <?php echo e($studentUser?->email); ?></dd></div>
                    <div><dt class="text-[11.5px] text-slate-500">Carrera</dt><dd class="font-semibold text-slate-800"><?php echo e($application->student?->career?->nombre ?? '—'); ?></dd></div>
                    <div>
                        <dt class="text-[11.5px] text-slate-500">Afinidad con la oferta</dt>
                        <dd class="flex items-center gap-2">
                            <div class="h-2 flex-1 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full <?php echo e($score >= 70 ? 'bg-emerald-500' : ($score >= 40 ? 'bg-amber-500' : 'bg-red-600')); ?>" style="width: <?php echo e(min($score, 100)); ?>%"></div>
                            </div>
                            <span class="font-bold text-slate-800"><?php echo e(round($score)); ?>%</span>
                        </dd>
                    </div>
                    <div><dt class="text-[11.5px] text-slate-500">Postuló el</dt><dd class="font-semibold text-slate-800"><?php echo e($application->created_at?->format('d-m-Y H:i')); ?></dd></div>
                    <?php if($application->decision_at): ?>
                        <div><dt class="text-[11.5px] text-slate-500">Resuelta el</dt><dd class="font-semibold text-slate-800"><?php echo e($application->decision_at->format('d-m-Y H:i')); ?><?php echo e($application->reviewer ? ' por '.trim($application->reviewer->name.' '.$application->reviewer->last_name) : ''); ?></dd></div>
                    <?php endif; ?>
                </dl>

                <div>
                    <p class="text-[11.5px] text-slate-500 mb-1">Carta de motivación</p>
                    <p class="whitespace-pre-line text-slate-700 leading-relaxed"><?php echo e($application->motivation); ?></p>
                </div>

                <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    <?php if($application->cv_path): ?>
                        <a href="<?php echo e(route('applications.cv', $application)); ?>" class="rounded-lg border border-slate-300 px-3 py-2 text-[12.5px] font-semibold text-slate-700 hover:bg-slate-50">Descargar CV</a>
                    <?php endif; ?>
                    <?php if($canReview): ?>
                        <form id="assign-application" action="<?php echo e(route('applications.assign', $application)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="button" onclick="confirmSubmit('assign-application', { title: '¿Asignar práctica?', text: 'Se creará la práctica y se avisará al estudiante.', confirmText: 'Sí, asignar', color: '#047857', icon: 'question' })"
                                class="rounded-lg bg-emerald-700 px-3 py-2 text-[12.5px] font-semibold text-white hover:bg-emerald-800 cursor-pointer">Asignar práctica</button>
                        </form>
                        <form id="reject-application" action="<?php echo e(route('applications.reject', $application)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="button" onclick="confirmSubmit('reject-application', { title: '¿Rechazar postulación?', text: 'Se avisará al estudiante.', confirmText: 'Sí, rechazar' })"
                                class="rounded-lg border border-red-200 px-3 py-2 text-[12.5px] font-semibold text-red-700 hover:bg-red-50 cursor-pointer">Rechazar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="lg:col-span-2 rounded-xl bg-white shadow-sm ring-1 ring-gray-200 flex flex-col">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-[15px] font-bold text-slate-800">Mensajes de la postulación</h2>
                    <p class="text-[12px] text-slate-500">Conversación entre el estudiante y la coordinación. Cada mensaje guarda el estado que tenía la postulación.</p>
                </div>

                <div class="flex-1 space-y-3 p-5 max-h-[460px] overflow-y-auto" id="messages">
                    <?php $__empty_1 = true; $__currentLoopData = $application->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $mine = $message->user_id === auth()->id(); ?>
                        <div class="flex <?php echo e($mine ? 'justify-end' : 'justify-start'); ?>">
                            <div class="max-w-[80%] rounded-xl px-4 py-2.5 <?php echo e($mine ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800'); ?>">
                                <p class="text-[11px] font-semibold <?php echo e($mine ? 'text-slate-300' : 'text-slate-500'); ?>">
                                    <?php echo e($message->user ? trim($message->user->name.' '.$message->user->last_name) : 'Usuario eliminado'); ?>

                                    · <?php echo e($message->created_at?->format('d-m-Y H:i')); ?> · <span class="italic"><?php echo e($message->application_status); ?></span>
                                </p>
                                <p class="mt-0.5 text-[13px] whitespace-pre-line"><?php echo e($message->body); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-center text-[13px] text-slate-500 py-10">Aún no hay mensajes. Escribe el primero para resolver dudas sobre esta postulación.</p>
                    <?php endif; ?>
                </div>

                <form action="<?php echo e(route('applications.messages.store', $application)); ?>" method="POST" class="border-t border-gray-100 p-4 flex gap-3 items-end">
                    <?php echo csrf_field(); ?>
                    <div class="flex-1">
                        <label for="message-body" class="sr-only">Mensaje</label>
                        <textarea id="message-body" name="body" rows="2" required maxlength="2000" placeholder="Escribe un mensaje..." class="form-input"><?php echo e(old('body')); ?></textarea>
                        <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-[12px] text-red-700"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="rounded-lg bg-[#C8102E] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#9e0b22] cursor-pointer">Enviar</button>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        const messages = document.getElementById('messages');
        messages.scrollTop = messages.scrollHeight;
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/applications/show.blade.php ENDPATH**/ ?>