<?php $__env->startSection('title', 'Estudiantes'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Estudiantes','description' => $can['manage'] ? 'Vincula las cuentas de estudiante con su carrera, nivel, promedio y habilidades. El coordinador valida cada perfil.' : 'Revisa y valida los datos académicos de los estudiantes de tu carrera.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Estudiantes','description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($can['manage'] ? 'Vincula las cuentas de estudiante con su carrera, nivel, promedio y habilidades. El coordinador valida cada perfil.' : 'Revisa y valida los datos académicos de los estudiantes de tu carrera.')]); ?>
            <?php if($can['manage']): ?>
             <?php $__env->slot('actions', null, []); ?> 
                <button type="button" onclick="openCreateModal('createStudentModal')"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo perfil
                </button>
             <?php $__env->endSlot(); ?>
            <?php endif; ?>
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

        <div class="grid gap-4 sm:grid-cols-3">
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Perfiles de estudiante','value' => $counts['total']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Perfiles de estudiante','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counts['total'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Validados por coordinador','value' => $counts['validados'],'color' => 'emerald']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Validados por coordinador','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counts['validados']),'color' => 'emerald']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Por validar','value' => $counts['por_validar'],'color' => 'amber']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Por validar','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counts['por_validar']),'color' => 'amber']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        </div>

        <?php if (isset($component)) { $__componentOriginal00be98f2cda19e6dc66afc21b5371ed8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal00be98f2cda19e6dc66afc21b5371ed8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-card','data' => ['title' => 'Listado de estudiantes','total' => $counts['total'],'searchId' => 'studentSearch','searchPlaceholder' => 'Buscar por nombre, ID, carrera o habilidad...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Listado de estudiantes','total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counts['total']),'search-id' => 'studentSearch','search-placeholder' => 'Buscar por nombre, ID, carrera o habilidad...']); ?>
             <?php $__env->slot('icon', null, []); ?> 
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
             <?php $__env->endSlot(); ?>

            <table id="studentsTable" class="w-full min-w-[960px] text-left text-[13px] text-slate-600">
                <thead class="bg-gray-50/60 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3.5">ID</th>
                        <th class="px-6 py-3.5">Estudiante</th>
                        <th class="px-6 py-3.5">Carrera</th>
                        <th class="px-6 py-3.5">Nivel / año / promedio</th>
                        <th class="px-6 py-3.5">Habilidades</th>
                        <th class="px-6 py-3.5 text-center">Postulaciones</th>
                        <th class="px-6 py-3.5 text-center">Validación</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $fullName = trim(($student->user?->name ?? '').' '.($student->user?->last_name ?? '')) ?: 'Sin cuenta';
                            $skills = $student->habilidades ?? [];
                            $rowData = [
                                'id_student' => $student->id_student,
                                'user_id' => $student->user_id,
                                'career_id' => $student->career_id,
                                'nivel_avance' => $student->nivel_avance,
                                'habilidades' => implode(', ', $skills),
                                'year_level' => $student->year_level,
                                'gpa' => $student->gpa,
                            ];
                        ?>
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4" data-order="<?php echo e($student->id_student); ?>">
                                <span class="px-2 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono text-[11.5px] font-semibold text-slate-600">#<?php echo e($student->id_student); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-[13.5px]"><?php echo e($fullName); ?></p>
                                <p class="text-[12px] text-slate-500"><?php echo e($student->user?->email); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($student->career): ?>
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono text-[11px] font-bold text-slate-700"><?php echo e($student->career->codigo); ?></span>
                                    <span class="ml-1 text-slate-700"><?php echo e($student->career->nombre); ?></span>
                                <?php else: ?>
                                    <span class="text-slate-500"><?php echo e($student->carrera ?: '—'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200"><?php echo e($student->nivel_avance); ?></span>
                                <p class="mt-1 text-[12px] text-slate-500">
                                    <?php echo e($student->year_level ? $student->year_level.'° año' : 'Año sin registrar'); ?> ·
                                    Promedio <b class="text-slate-700"><?php echo e($student->gpa !== null ? number_format((float) $student->gpa, 1, ',', '') : '—'); ?></b>
                                </p>
                            </td>
                            <td class="px-6 py-4" data-search="<?php echo e(implode(' ', $skills)); ?>">
                                <div class="flex flex-wrap gap-1 max-w-[240px]">
                                    <?php $__empty_1 = true; $__currentLoopData = array_slice($skills, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-[11px] font-semibold text-slate-600"><?php echo e($skill); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span class="text-slate-400 text-[12px]">Sin habilidades</span>
                                    <?php endif; ?>
                                    <?php if(count($skills) > 3): ?>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11px] font-bold text-slate-500" title="<?php echo e(implode(', ', array_slice($skills, 3))); ?>">+<?php echo e(count($skills) - 3); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center" data-order="<?php echo e($student->applications_count); ?>">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[12px] font-bold <?php echo e($student->applications_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200'); ?>">
                                    <?php echo e($student->applications_count); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($student->validated_at): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                                        title="<?php echo e($student->validator ? 'Por '.trim($student->validator->name.' '.$student->validator->last_name) : ''); ?> el <?php echo e($student->validated_at->format('d-m-Y')); ?>">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Validado
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Por validar</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <?php if($can['validate'] && ! $student->validated_at): ?>
                                        <form id="validate-student-<?php echo e($student->id); ?>" action="<?php echo e(route('students.validate', $student)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'approve','title' => 'Validar datos del estudiante','onclick' => 'confirmSubmit(\'validate-student-'.e($student->id).'\', { title: \'¿Validar perfil?\', text: '.e(json_encode('Confirmas que los datos académicos de '.$fullName.' son correctos.')).', confirmText: \'Sí, validar\', color: \'#047857\', icon: \'question\' })']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'approve','title' => 'Validar datos del estudiante','onclick' => 'confirmSubmit(\'validate-student-'.e($student->id).'\', { title: \'¿Validar perfil?\', text: '.e(json_encode('Confirmas que los datos académicos de '.$fullName.' son correctos.')).', confirmText: \'Sí, validar\', color: \'#047857\', icon: \'question\' })']); ?>
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
                                    <?php if($can['manage']): ?>
                                    <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'edit','title' => 'Editar perfil','onclick' => 'openEditModal(\'editStudentModal\', '.e(json_encode(route('students.update', $student))).', '.e(json_encode($rowData)).', '.e(json_encode('#'.$student->id_student.' · '.$fullName)).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'edit','title' => 'Editar perfil','onclick' => 'openEditModal(\'editStudentModal\', '.e(json_encode(route('students.update', $student))).', '.e(json_encode($rowData)).', '.e(json_encode('#'.$student->id_student.' · '.$fullName)).')']); ?>
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
                                    <form id="delete-student-<?php echo e($student->id); ?>" action="<?php echo e(route('students.destroy', $student)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'delete','title' => 'Eliminar perfil','onclick' => 'confirmSubmit(\'delete-student-'.e($student->id).'\', { title: \'¿Eliminar perfil?\', text: '.e(json_encode('Se eliminará el perfil de '.$fullName.'. La cuenta de usuario se conserva.')).', confirmText: \'Sí, eliminar\' })']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'delete','title' => 'Eliminar perfil','onclick' => 'confirmSubmit(\'delete-student-'.e($student->id).'\', { title: \'¿Eliminar perfil?\', text: '.e(json_encode('Se eliminará el perfil de '.$fullName.'. La cuenta de usuario se conserva.')).', confirmText: \'Sí, eliminar\' })']); ?>
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

    <datalist id="nivelesAvance">
        <option value="Inicial"></option>
        <option value="Intermedio"></option>
        <option value="Avanzado"></option>
        <option value="Egresado"></option>
    </datalist>

    <?php if($can['manage']): ?>
    <?php $__currentLoopData = ['create' => 'Nuevo perfil de estudiante', 'edit' => 'Editar perfil de estudiante']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode => $modalTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $modalId = $mode.'StudentModal'; ?>
        <?php if (isset($component)) { $__componentOriginaldf85e6552b95df616d59caebcbf2473d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf85e6552b95df616d59caebcbf2473d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-modal','data' => ['id' => $modalId,'title' => $modalTitle,'subtitle' => 'Cuenta, carrera, nivel y habilidades del estudiante.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalId),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalTitle),'subtitle' => 'Cuenta, carrera, nivel y habilidades del estudiante.']); ?>
            <form action="<?php echo e($mode === 'create' ? route('students.store') : old('_action', '#')); ?>" method="POST" class="space-y-5 p-6">
                <?php echo csrf_field(); ?>
                <?php if($mode === 'edit'): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
                <input type="hidden" name="_modal" value="<?php echo e($modalId); ?>">
                <input type="hidden" name="_action" value="<?php echo e(old('_action')); ?>">
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
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'ID de estudiante','for' => ''.e($mode).'-id_student','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'ID de estudiante','for' => ''.e($mode).'-id_student','required' => true]); ?>
                        <input type="number" id="<?php echo e($mode); ?>-id_student" name="id_student" required min="1" value="<?php echo e($old('id_student')); ?>" placeholder="Ej: 20261001" class="form-input font-mono">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Cuenta de estudiante','for' => ''.e($mode).'-user_id','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Cuenta de estudiante','for' => ''.e($mode).'-user_id','required' => true]); ?>
                        <select id="<?php echo e($mode); ?>-user_id" name="user_id" required class="form-input">
                            <option value="">Selecciona una cuenta</option>
                            <?php $__currentLoopData = $studentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($account->id); ?>" <?php if((string) $old('user_id') === (string) $account->id): echo 'selected'; endif; ?>>
                                    <?php echo e(trim($account->name.' '.$account->last_name)); ?> (<?php echo e($account->email); ?>)<?php echo e(in_array($account->id, $usedUserIds) ? ' · ya tiene perfil' : ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Carrera','for' => ''.e($mode).'-career_id','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Carrera','for' => ''.e($mode).'-career_id','required' => true]); ?>
                        <select id="<?php echo e($mode); ?>-career_id" name="career_id" required class="form-input">
                            <option value="">Selecciona una carrera</option>
                            <?php $__currentLoopData = $careers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $career): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($career->id); ?>" <?php if((string) $old('career_id') === (string) $career->id): echo 'selected'; endif; ?>><?php echo e($career->codigo); ?> · <?php echo e($career->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Nivel de avance','for' => ''.e($mode).'-nivel_avance','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Nivel de avance','for' => ''.e($mode).'-nivel_avance','required' => true]); ?>
                        <input type="text" id="<?php echo e($mode); ?>-nivel_avance" name="nivel_avance" list="nivelesAvance" required maxlength="120" value="<?php echo e($old('nivel_avance')); ?>" placeholder="Ej: Avanzado" class="form-input">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Año que cursa','for' => ''.e($mode).'-year_level']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Año que cursa','for' => ''.e($mode).'-year_level']); ?>
                        <select id="<?php echo e($mode); ?>-year_level" name="year_level" class="form-input">
                            <option value="">Sin registrar</option>
                            <?php for($year = 1; $year <= 7; $year++): ?>
                                <option value="<?php echo e($year); ?>" <?php if((string) $old('year_level') === (string) $year): echo 'selected'; endif; ?>><?php echo e($year); ?>° año</option>
                            <?php endfor; ?>
                        </select>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Promedio de notas','for' => ''.e($mode).'-gpa','hint' => 'Escala 1,0 a 7,0. Algunas ofertas exigen un promedio mínimo.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Promedio de notas','for' => ''.e($mode).'-gpa','hint' => 'Escala 1,0 a 7,0. Algunas ofertas exigen un promedio mínimo.']); ?>
                        <input type="number" id="<?php echo e($mode); ?>-gpa" name="gpa" min="1" max="7" step="0.1" value="<?php echo e($old('gpa')); ?>" placeholder="Ej: 5.6" class="form-input">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => 'Habilidades','for' => ''.e($mode).'-habilidades','class' => 'sm:col-span-2','hint' => 'Sepáralas con comas. Se usan para calcular la afinidad con las ofertas.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Habilidades','for' => ''.e($mode).'-habilidades','class' => 'sm:col-span-2','hint' => 'Sepáralas con comas. Se usan para calcular la afinidad con las ofertas.']); ?>
                        <input type="text" id="<?php echo e($mode); ?>-habilidades" name="habilidades" value="<?php echo e(is_array($old('habilidades')) ? implode(', ', $old('habilidades')) : $old('habilidades')); ?>" placeholder="PHP, Laravel, SQL" class="form-input">
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

                <?php if (isset($component)) { $__componentOriginal98d29adaa4406bec3dfa33b112d9a220 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98d29adaa4406bec3dfa33b112d9a220 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-actions','data' => ['modal' => $modalId,'label' => $mode === 'create' ? 'Crear perfil' : 'Guardar cambios']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalId),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode === 'create' ? 'Crear perfil' : 'Guardar cambios')]); ?>
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        initDataTable('#studentsTable', '#studentSearch', { order: [[0, 'asc']] });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/students/index.blade.php ENDPATH**/ ?>