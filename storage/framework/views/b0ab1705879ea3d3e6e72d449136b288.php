
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'total', 'searchId', 'searchPlaceholder' => 'Buscar...']));

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

foreach (array_filter((['title', 'total', 'searchId', 'searchPlaceholder' => 'Buscar...']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
    <div class="flex flex-col gap-4 border-b border-gray-100 p-4 sm:p-5 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 text-red-700 flex items-center justify-center shrink-0">
                <?php echo e($icon ?? ''); ?>

            </div>
            <div>
                <h2 class="text-[15px] font-bold text-slate-800"><?php echo e($title); ?></h2>
                <p class="text-[12px] text-slate-500">Registros: <strong class="text-slate-700"><?php echo e($total); ?></strong></p>
            </div>
        </div>

        <div class="relative w-full md:max-w-sm">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" id="<?php echo e($searchId); ?>" placeholder="<?php echo e($searchPlaceholder); ?>"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-[13px] text-slate-800 placeholder-slate-400 focus:bg-white focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-100 transition">
        </div>
    </div>

    <div class="overflow-x-auto">
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/components/table-card.blade.php ENDPATH**/ ?>