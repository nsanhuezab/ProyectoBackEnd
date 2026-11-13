
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'value', 'color' => 'slate']));

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

foreach (array_filter((['label', 'value', 'color' => 'slate']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $colors = [
        'slate' => 'text-slate-900',
        'emerald' => 'text-emerald-700',
        'amber' => 'text-amber-600',
        'red' => 'text-red-700',
        'blue' => 'text-blue-700',
        'muted' => 'text-slate-500',
    ];
?>

<div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500"><?php echo e($label); ?></p>
    <p class="mt-1 text-2xl font-extrabold <?php echo e($colors[$color] ?? $colors['slate']); ?>"><?php echo e($value); ?></p>
</div>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/components/stat-card.blade.php ENDPATH**/ ?>