
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'description' => null, 'section' => 'Panel de administración']));

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

foreach (array_filter((['title', 'description' => null, 'section' => 'Panel de administración']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="space-y-4">
    <nav class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500" aria-label="Ruta">
        <span><?php echo e($section); ?></span>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-medium text-slate-800"><?php echo e($title); ?></span>
    </nav>

    <header class="flex flex-col gap-4 border-b-2 border-slate-800/80 pb-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700"><?php echo e($section); ?></p>
            <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900"><?php echo e($title); ?></h1>
            <?php if($description): ?>
                <p class="mt-1 text-sm text-slate-500"><?php echo e($description); ?></p>
            <?php endif; ?>
        </div>

        <?php if(isset($actions)): ?>
            <div class="flex flex-wrap items-center gap-2"><?php echo e($actions); ?></div>
        <?php endif; ?>
    </header>
</div>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/components/page-header.blade.php ENDPATH**/ ?>