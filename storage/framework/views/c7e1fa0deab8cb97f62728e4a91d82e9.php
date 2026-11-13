<?php
    $application = $practice->application;
    $student = $application?->student;
    $studentUser = $student?->user;
    $offer = $application?->offer;
    $company = $offer?->company;
    $career = $student?->career;
    $supervisorEvaluation = $practice->evaluationBy(\App\Models\PracticeEvaluation::SUPERVISOR);
    $coordinatorEvaluation = $practice->evaluationBy(\App\Models\PracticeEvaluation::COORDINATOR);
    $grade = fn ($value) => $value === null ? '—' : number_format((float) $value, 1, ',', '');
    $percent = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, ',', ''), '0'), ',').'%';
    $approved = $practice->isApproved();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de cierre de práctica #<?php echo e($practice->id); ?></title>
    <style>
        @page { margin: 32px 40px; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1e293b; }
        .header { border-bottom: 3px solid #C8102E; padding-bottom: 8px; margin-bottom: 16px; }
        .header .brand { color: #C8102E; font-size: 20px; font-weight: bold; }
        .header .sub { color: #475569; font-size: 10px; }
        h1 { font-size: 16px; text-align: center; margin: 4px 0 2px; }
        .folio { text-align: center; color: #64748b; font-size: 10px; margin-bottom: 14px; }
        h2 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #C8102E; margin: 16px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        .data td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .data td.label { width: 32%; color: #64748b; }
        .grid th, .grid td { border: 1px solid #cbd5e1; padding: 5px 6px; }
        .grid th { background: #0f172a; color: #fff; font-size: 10px; }
        .grid td.num { text-align: center; font-weight: bold; }
        .grid tr.total td { background: #f1f5f9; font-weight: bold; }
        .final { margin-top: 14px; border: 2px solid #0f172a; padding: 10px; text-align: center; }
        .final .grade { font-size: 26px; font-weight: bold; color: <?php echo e($approved ? '#047857' : '#C8102E'); ?>; }
        .signatures { margin-top: 56px; }
        .signatures td { width: 33%; text-align: center; padding: 0 10px; font-size: 10px; color: #475569; }
        .signatures .line { border-top: 1px solid #0f172a; padding-top: 4px; }
        .footer { margin-top: 24px; font-size: 9px; color: #94a3b8; text-align: center; }
        .print-bar { text-align: right; margin-bottom: 12px; }
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
        <div class="sub">Universidad Católica de la Santísima Concepción · Plataforma de Prácticas Profesionales</div>
    </div>

    <h1>Acta de cierre de práctica profesional</h1>
    <div class="folio">Folio N° <?php echo e(str_pad((string) $practice->id, 6, '0', STR_PAD_LEFT)); ?> · Emitida el <?php echo e($generatedAt->format('d-m-Y H:i')); ?></div>

    <h2>Datos del estudiante</h2>
    <table class="data">
        <tr><td class="label">Nombre</td><td><?php echo e(trim(($studentUser?->name ?? '').' '.($studentUser?->last_name ?? ''))); ?></td></tr>
        <tr><td class="label">RUT</td><td><?php echo e($studentUser?->rut ?? '—'); ?></td></tr>
        <tr><td class="label">ID de estudiante</td><td><?php echo e($student?->id_student); ?></td></tr>
        <tr><td class="label">Carrera</td><td><?php echo e($career?->nombre); ?> (<?php echo e($career?->codigo); ?>)</td></tr>
    </table>

    <h2>Datos de la empresa</h2>
    <table class="data">
        <tr><td class="label">Razón social</td><td><?php echo e($company?->razon_social ?? $offer?->empresa); ?></td></tr>
        <tr><td class="label">RUT</td><td><?php echo e($company?->rut ?? '—'); ?></td></tr>
        <tr><td class="label">Rubro</td><td><?php echo e($company?->rubro ?? '—'); ?></td></tr>
        <tr><td class="label">Supervisor de práctica</td><td><?php echo e($company?->contacto_nombre ?? '—'); ?> (<?php echo e($company?->contacto_email ?? '—'); ?>)</td></tr>
    </table>

    <h2>Práctica</h2>
    <table class="data">
        <tr><td class="label">Cargo / oferta</td><td><?php echo e($offer?->titulo); ?></td></tr>
        <tr><td class="label">Área y modalidad</td><td><?php echo e($offer?->area); ?> · <?php echo e($offer?->modalidad); ?></td></tr>
        <tr><td class="label">Período</td><td><?php echo e($practice->fecha_inicio?->format('d-m-Y') ?? '—'); ?> al <?php echo e($practice->fecha_termino?->format('d-m-Y') ?? '—'); ?></td></tr>
        <tr><td class="label">Horas registradas en bitácora</td><td><?php echo e($practice->logs->sum('hours')); ?> horas (<?php echo e($practice->logs->count()); ?> registros quincenales)</td></tr>
    </table>

    <h2>Competencias evaluadas</h2>
    <table class="grid">
        <thead>
            <tr>
                <th style="text-align:left">Competencia</th>
                <th>Supervisor</th>
                <th>Coordinador</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $competencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($label); ?></td>
                    <td class="num"><?php echo e($grade($supervisorEvaluation?->scores[$key] ?? null)); ?></td>
                    <td class="num"><?php echo e($grade($coordinatorEvaluation?->scores[$key] ?? null)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total">
                <td>Nota de cada evaluador</td>
                <td class="num"><?php echo e($grade($supervisorEvaluation?->grade)); ?></td>
                <td class="num"><?php echo e($grade($coordinatorEvaluation?->grade)); ?></td>
            </tr>
            <tr class="total">
                <td>Ponderación definida por la carrera</td>
                <td class="num"><?php echo e($percent($career?->supervisor)); ?></td>
                <td class="num"><?php echo e($percent($career?->coordinador)); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="final">
        <div>Calificación final de la práctica</div>
        <div class="grade"><?php echo e($grade($practice->final_grade)); ?></div>
        <div><?php echo e($approved ? 'APROBADA' : 'REPROBADA'); ?> (nota mínima de aprobación <?php echo e(number_format((float) config('practices.passing_grade'), 1, ',', '')); ?>)</div>
    </div>

    <table class="signatures">
        <tr>
            <td><div class="line">Estudiante</div></td>
            <td><div class="line">Supervisor de la empresa</div></td>
            <td><div class="line">Coordinador académico</div></td>
        </tr>
    </table>

    <div class="footer">Documento generado automáticamente por la Plataforma de Prácticas Profesionales UCSC.</div>
</body>
</html>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/practices/acta.blade.php ENDPATH**/ ?>