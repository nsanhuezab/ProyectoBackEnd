
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status']));

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

foreach (array_filter((['status']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $styles = [
        'Pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Aprobada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Rechazada' => 'bg-red-50 text-red-700 border-red-200',
        'Postulado' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Rechazado' => 'bg-red-50 text-red-700 border-red-200',
        'Asignado' => 'bg-violet-50 text-violet-700 border-violet-200',
        'En Curso' => 'bg-sky-50 text-sky-700 border-sky-200',
        'Informe Entregado' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Evaluado' => 'bg-rose-50 text-rose-700 border-rose-200',
        'Finalizada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];
?>

<span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-[11.5px] font-bold border <?php echo e($styles[$status] ?? 'bg-slate-100 text-slate-600 border-slate-200'); ?>">
    <?php echo e($status); ?>

</span>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/components/status-badge.blade.php ENDPATH**/ ?>