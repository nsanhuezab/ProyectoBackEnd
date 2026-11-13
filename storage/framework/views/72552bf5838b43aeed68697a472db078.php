
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type', 'title']));

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

foreach (array_filter((['type', 'title']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $styles = [
        'view' => 'border-gray-300 text-gray-600 hover:bg-gray-50',
        'edit' => 'border-slate-300 text-slate-700 hover:bg-slate-100',
        'delete' => 'border-red-200 text-red-600 hover:bg-red-50',
        'approve' => 'border-emerald-200 text-emerald-700 hover:bg-emerald-50',
        'reject' => 'border-amber-200 text-amber-700 hover:bg-amber-50',
        'next' => 'border-sky-200 text-sky-700 hover:bg-sky-50',
        'download' => 'border-gray-300 text-gray-600 hover:bg-gray-50',
    ];
    $paths = [
        'view' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'edit' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
        'delete' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        'approve' => 'M5 13l4 4L19 7',
        'reject' => 'M6 18L18 6M6 6l12 12',
        'next' => 'M13 7l5 5m0 0l-5 5m5-5H6',
        'download' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
    ];
?>

<button type="button" title="<?php echo e($title); ?>" aria-label="<?php echo e($title); ?>"
    <?php echo e($attributes->merge(['class' => 'inline-flex h-8 w-8 items-center justify-center rounded-lg border bg-white shadow-sm transition cursor-pointer '.($styles[$type] ?? $styles['view'])])); ?>>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($paths[$type] ?? $paths['view']); ?>" />
    </svg>
</button>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/components/action-button.blade.php ENDPATH**/ ?>