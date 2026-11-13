<?php
    $percent = fn ($value) => $value === null ? 'Sin datos' : number_format($value, 1, ',', '').'%';
    $groups = [
        'Estudiantes por carrera' => [$byCareer, 'Carrera'],
        'Estudiantes por empresa' => [$byCompany, 'Empresa'],
        'Distribución por modalidad' => [$byModality, 'Modalidad'],
        'Distribución por rubro' => [$byIndustry, 'Rubro'],
    ];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Indicadores de prácticas</title>
    <style>
        @page { margin: 30px 36px; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10.5px; color: #1e293b; }
        .header { border-bottom: 3px solid #C8102E; padding-bottom: 6px; margin-bottom: 12px; }
        .brand { color: #C8102E; font-size: 18px; font-weight: bold; }
        h1 { font-size: 15px; margin: 6px 0 2px; }
        .meta { color: #64748b; font-size: 9.5px; margin-bottom: 10px; }
        h2 { font-size: 11px; color: #C8102E; text-transform: uppercase; letter-spacing: 1px; margin: 14px 0 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 4px 6px; }
        th { background: #0f172a; color: #fff; text-align: left; font-size: 9.5px; }
        td.num { text-align: center; }
        .kpis td { width: 25%; text-align: center; border: 1px solid #e2e8f0; padding: 8px; }
        .kpis .value { font-size: 17px; font-weight: bold; color: #0f172a; }
        .kpis .label { font-size: 9px; color: #64748b; text-transform: uppercase; }
        .print-bar { text-align: right; margin-bottom: 10px; }
        .print-bar button { background: #C8102E; color: #fff; border: 0; padding: 8px 14px; border-radius: 6px; cursor: pointer; }
        @media print { .print-bar { display: none; } }
    </style>
</head>
<body>
    <?php if(! empty($printable)): ?>
        <div class="print-bar"><button onclick="window.print()">Imprimir / Guardar como PDF</button></div>
    <?php endif; ?>

    <div class="header">
        <div class="brand">UCSC</div>
        <h1>Panel de indicadores de prácticas profesionales</h1>
        <div class="meta">Alcance: <?php echo e($scope ?? 'Todas las carreras'); ?> · Generado el <?php echo e($generatedAt->format('d-m-Y H:i')); ?></div>
    </div>

    <table class="kpis">
        <tr>
            <td><div class="value"><?php echo e($totals['activas']); ?></div><div class="label">Estudiantes en práctica</div></td>
            <td><div class="value"><?php echo e($percent($totals['aprobacion'])); ?></div><div class="label">Tasa de aprobación</div></td>
            <td><div class="value"><?php echo e($percent($totals['empleabilidad'])); ?></div><div class="label">Empleabilidad post-práctica</div></td>
            <td><div class="value"><?php echo e($totals['promedio'] === null ? '—' : number_format($totals['promedio'], 1, ',', '')); ?></div><div class="label">Nota final promedio</div></td>
        </tr>
    </table>
    <p class="meta"><?php echo e($totals['practicas']); ?> prácticas registradas · <?php echo e($totals['evaluadas']); ?> con nota final · <?php echo e($totals['finalizadas']); ?> finalizadas · <?php echo e($totals['contratados']); ?> contratados por su empresa.</p>

    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => [$rows, $column]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h2><?php echo e($title); ?></h2>
        <table>
            <thead><tr><th><?php echo e($column); ?></th><th style="width:22%">En práctica ahora</th><th style="width:22%">Total histórico</th></tr></thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr><td><?php echo e($row['nombre']); ?></td><td class="num"><?php echo e($row['activas']); ?></td><td class="num"><?php echo e($row['total']); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3">Sin datos.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <h2>Ranking de empresas evaluadas por los estudiantes</h2>
    <table>
        <thead><tr><th style="width:6%">#</th><th>Empresa</th><th>Rubro</th><th style="width:16%">Promedio (1-5)</th><th style="width:14%">Evaluaciones</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $ranking; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr><td class="num"><?php echo e($position + 1); ?></td><td><?php echo e($row['empresa']); ?></td><td><?php echo e($row['rubro']); ?></td><td class="num"><?php echo e(number_format($row['promedio'], 1, ',', '')); ?></td><td class="num"><?php echo e($row['evaluaciones']); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5">Todavía no hay evaluaciones de empresas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/reports/pdf.blade.php ENDPATH**/ ?>