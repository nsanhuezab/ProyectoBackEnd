@php
    $percent = fn ($value) => $value === null ? 'Sin datos' : number_format($value, 1, ',', '').'%';
    $groups = [
        'Estudiantes por carrera' => [$byCareer, 'Carrera'],
        'Estudiantes por empresa' => [$byCompany, 'Empresa'],
        'Distribución por modalidad' => [$byModality, 'Modalidad'],
        'Distribución por rubro' => [$byIndustry, 'Rubro'],
    ];
@endphp
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
    @if (! empty($printable))
        <div class="print-bar"><button onclick="window.print()">Imprimir / Guardar como PDF</button></div>
    @endif

    <div class="header">
        <div class="brand">UCSC</div>
        <h1>Panel de indicadores de prácticas profesionales</h1>
        <div class="meta">Alcance: {{ $scope ?? 'Todas las carreras' }} · Generado el {{ $generatedAt->format('d-m-Y H:i') }}</div>
    </div>

    <table class="kpis">
        <tr>
            <td><div class="value">{{ $totals['activas'] }}</div><div class="label">Estudiantes en práctica</div></td>
            <td><div class="value">{{ $percent($totals['aprobacion']) }}</div><div class="label">Tasa de aprobación</div></td>
            <td><div class="value">{{ $percent($totals['empleabilidad']) }}</div><div class="label">Empleabilidad post-práctica</div></td>
            <td><div class="value">{{ $totals['promedio'] === null ? '—' : number_format($totals['promedio'], 1, ',', '') }}</div><div class="label">Nota final promedio</div></td>
        </tr>
    </table>
    <p class="meta">{{ $totals['practicas'] }} prácticas registradas · {{ $totals['evaluadas'] }} con nota final · {{ $totals['finalizadas'] }} finalizadas · {{ $totals['contratados'] }} contratados por su empresa.</p>

    @foreach ($groups as $title => [$rows, $column])
        <h2>{{ $title }}</h2>
        <table>
            <thead><tr><th>{{ $column }}</th><th style="width:22%">En práctica ahora</th><th style="width:22%">Total histórico</th></tr></thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr><td>{{ $row['nombre'] }}</td><td class="num">{{ $row['activas'] }}</td><td class="num">{{ $row['total'] }}</td></tr>
                @empty
                    <tr><td colspan="3">Sin datos.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

    <h2>Ranking de empresas evaluadas por los estudiantes</h2>
    <table>
        <thead><tr><th style="width:6%">#</th><th>Empresa</th><th>Rubro</th><th style="width:16%">Promedio (1-5)</th><th style="width:14%">Evaluaciones</th></tr></thead>
        <tbody>
            @forelse ($ranking as $position => $row)
                <tr><td class="num">{{ $position + 1 }}</td><td>{{ $row['empresa'] }}</td><td>{{ $row['rubro'] }}</td><td class="num">{{ number_format($row['promedio'], 1, ',', '') }}</td><td class="num">{{ $row['evaluaciones'] }}</td></tr>
            @empty
                <tr><td colspan="5">Todavía no hay evaluaciones de empresas.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
