{{-- CV de ejemplo que genera DemoCvSeeder. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CV {{ $cv['nombre'] }}</title>
    <style>
        @page { margin: 36px 44px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1f2937; line-height: 1.45; }
        .header { border-bottom: 3px solid #b91c1c; padding-bottom: 10px; margin-bottom: 14px; }
        h1 { font-size: 22px; margin: 0; color: #111827; }
        .role { font-size: 12px; color: #b91c1c; font-weight: bold; margin-top: 2px; }
        .contact { margin-top: 6px; color: #4b5563; }
        h2 { font-size: 11.5px; text-transform: uppercase; letter-spacing: 1px; color: #b91c1c; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; margin: 16px 0 7px; }
        .item { margin-bottom: 8px; }
        .item-title { font-weight: bold; color: #111827; }
        .muted { color: #6b7280; }
        ul { margin: 3px 0 0 16px; padding: 0; }
        li { margin-bottom: 2px; }
        .tag { display: inline-block; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 3px; padding: 1px 6px; margin: 0 4px 4px 0; }
        .note { margin-top: 22px; font-size: 8.5px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $cv['nombre'] }}</h1>
        <div class="role">{{ $cv['titulo'] }}</div>
        <div class="contact">{{ $cv['email'] }} &nbsp;·&nbsp; {{ $cv['telefono'] }} &nbsp;·&nbsp; Concepción, Chile</div>
    </div>

    <h2>Perfil</h2>
    <p>{{ $cv['perfil'] }}</p>

    <h2>Formación</h2>
    <div class="item">
        <div class="item-title">{{ $cv['carrera'] }}</div>
        <div class="muted">Universidad Católica de la Santísima Concepción · {{ $cv['anio'] }}° año · Promedio {{ number_format($cv['promedio'], 1, ',', '') }}</div>
    </div>

    <h2>Proyectos</h2>
    @foreach ($cv['proyectos'] as $proyecto)
        <div class="item">
            <div class="item-title">{{ $proyecto['nombre'] }} <span class="muted">· {{ $proyecto['periodo'] }}</span></div>
            <ul>
                @foreach ($proyecto['detalle'] as $linea)
                    <li>{{ $linea }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach

    <h2>Habilidades</h2>
    <div>
        @foreach ($cv['habilidades'] as $habilidad)
            <span class="tag">{{ $habilidad }}</span>
        @endforeach
    </div>

    <h2>Idiomas</h2>
    <p>{{ $cv['idiomas'] }}</p>

    <div class="note">CV ficticio generado para los datos de demostración de la plataforma.</div>
</body>
</html>
