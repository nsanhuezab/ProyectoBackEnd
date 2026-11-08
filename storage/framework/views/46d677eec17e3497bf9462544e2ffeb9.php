
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'for' => null, 'required' => false, 'hint' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['label', 'for' => null, 'required' => false, 'hint' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes); ?>>
    <label <?php if($for): ?> for="<?php echo e($for); ?>" <?php endif; ?> class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
        <?php echo e($label); ?> <?php if($required): ?><span class="text-red-600">*</span><?php endif; ?>
    </label>
    <?php echo e($slot); ?>

    <?php if($hint): ?>
        <p class="mt-1 text-[11.5px] text-slate-500"><?php echo e($hint); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/components/field.blade.php ENDPATH**/ ?>