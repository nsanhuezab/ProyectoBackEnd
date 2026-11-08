
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['id', 'title', 'subtitle' => null, 'width' => 'max-w-xl']));

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

foreach (array_filter((['id', 'title', 'subtitle' => null, 'width' => 'max-w-xl']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div id="<?php echo e($id); ?>" class="app-modal hidden fixed inset-0 z-[60] items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
    <div class="w-full <?php echo e($width); ?> max-h-[92vh] overflow-y-auto rounded-xl bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
            <div>
                <h2 class="font-bold" data-modal-title><?php echo e($title); ?></h2>
                <p class="text-[12px] text-slate-400" data-modal-subtitle><?php echo e($subtitle); ?></p>
            </div>
            <button type="button" onclick="closeModal('<?php echo e($id); ?>')" class="text-slate-300 hover:text-white cursor-pointer" aria-label="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/components/form-modal.blade.php ENDPATH**/ ?>