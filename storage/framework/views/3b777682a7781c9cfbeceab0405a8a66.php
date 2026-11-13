
<?php
    $isThisForm = old('_modal') === $prefix;
    $inputClass = 'w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100 transition';
?>

<input type="hidden" name="_modal" value="<?php echo e($prefix); ?>">

<?php if($isThisForm && $errors->any()): ?>
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[12.5px] text-red-800" role="alert">
        <p class="font-bold mb-1">Revisa los siguientes campos:</p>
        <ul class="list-disc pl-5 space-y-0.5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label for="<?php echo e($prefix); ?>-codigo" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
            Código <span class="text-red-600">*</span>
        </label>
        <input type="text" name="codigo" id="<?php echo e($prefix); ?>-codigo" required maxlength="20"
            value="<?php echo e($isThisForm ? old('codigo') : ''); ?>" placeholder="Ej: ICI"
            class="<?php echo e($inputClass); ?> uppercase font-mono">
    </div>

    <div class="sm:col-span-2">
        <label for="<?php echo e($prefix); ?>-nombre" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
            Nombre de la carrera <span class="text-red-600">*</span>
        </label>
        <input type="text" name="nombre" id="<?php echo e($prefix); ?>-nombre" required maxlength="255"
            value="<?php echo e($isThisForm ? old('nombre') : ''); ?>" placeholder="Ej: Ingeniería Civil Informática"
            class="<?php echo e($inputClass); ?>">
    </div>
</div>

<div>
    <p class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
        Ponderación de la evaluación <span class="text-red-600">*</span>
    </p>
    <div class="grid gap-4 sm:grid-cols-2 rounded-lg border border-slate-200 bg-slate-50/60 p-3.5">
        <div>
            <label for="<?php echo e($prefix); ?>-supervisor" class="block text-[12px] font-semibold text-slate-600 mb-1">Supervisor (%)</label>
            <input type="number" name="supervisor" id="<?php echo e($prefix); ?>-supervisor" required min="0" max="100" step="0.01"
                value="<?php echo e($isThisForm ? old('supervisor') : 50); ?>" data-weight="supervisor" data-prefix="<?php echo e($prefix); ?>"
                class="<?php echo e($inputClass); ?> bg-white">
        </div>
        <div>
            <label for="<?php echo e($prefix); ?>-coordinador" class="block text-[12px] font-semibold text-slate-600 mb-1">Coordinador (%)</label>
            <input type="number" name="coordinador" id="<?php echo e($prefix); ?>-coordinador" required min="0" max="100" step="0.01"
                value="<?php echo e($isThisForm ? old('coordinador') : 50); ?>" data-weight="coordinador" data-prefix="<?php echo e($prefix); ?>"
                class="<?php echo e($inputClass); ?> bg-white">
        </div>
        <p class="sm:col-span-2 text-[11.5px] text-slate-500">
            Ambos porcentajes deben sumar 100. Al cambiar uno, el otro se ajusta solo.
        </p>
    </div>
</div>

<label for="<?php echo e($prefix); ?>-activa" class="flex items-center gap-3 rounded-lg border border-slate-200 px-3.5 py-3 cursor-pointer hover:bg-slate-50">
    <input type="hidden" name="activa" value="0">
    <input type="checkbox" name="activa" id="<?php echo e($prefix); ?>-activa" value="1"
        <?php if($isThisForm ? old('activa') : true): echo 'checked'; endif; ?>
        class="h-4 w-4 rounded border-slate-300 text-red-700 focus:ring-red-600">
    <span>
        <span class="block text-[13px] font-bold text-slate-700">Carrera activa</span>
        <span class="block text-[11.5px] text-slate-500">Desmárcala si la carrera ya no se imparte; sus datos se conservan.</span>
    </span>
</label>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/careers/partials/form-fields.blade.php ENDPATH**/ ?>