<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $info['title'] }}</title>
    <style>
        :root { --ink:#0f172a; --muted:#64748b; --line:#e2e8f0; --bg:#f8fafc; --red:#C8102E; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; color:var(--ink); background:var(--bg); line-height:1.5; }
        a { color:inherit; }
        .layout { display:flex; min-height:100vh; }
        aside { width:290px; flex-shrink:0; background:#fff; border-right:1px solid var(--line); padding:20px 16px; position:sticky; top:0; height:100vh; overflow-y:auto; }
        aside h2 { font-size:13px; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); margin:22px 0 6px; }
        aside a { display:block; text-decoration:none; font-size:13px; padding:5px 8px; border-radius:6px; color:#334155; }
        aside a:hover { background:#f1f5f9; }
        main { flex:1; max-width:980px; padding:32px 40px 80px; }
        h1 { font-size:28px; margin:0 0 6px; }
        .lead { color:var(--muted); margin:0 0 20px; }
        .box { background:#fff; border:1px solid var(--line); border-radius:12px; padding:16px 20px; margin-bottom:18px; }
        .group { margin-top:42px; border-top:3px solid var(--ink); padding-top:14px; }
        .group h2 { margin:0; font-size:22px; }
        .tag { display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:99px; vertical-align:middle; }
        .tag.public { background:#dcfce7; color:#166534; } .tag.private { background:#fee2e2; color:#991b1b; }
        .ep { background:#fff; border:1px solid var(--line); border-radius:12px; margin:18px 0; overflow:hidden; }
        .ep > header { display:flex; flex-wrap:wrap; gap:10px; align-items:center; padding:12px 18px; border-bottom:1px solid var(--line); background:#f8fafc; }
        .m { font-weight:800; font-size:12px; padding:3px 9px; border-radius:6px; color:#fff; }
        .GET { background:#0369a1; } .POST { background:#15803d; } .PUT { background:#b45309; } .DELETE { background:#b91c1c; }
        code, pre { font-family: ui-monospace, "Cascadia Code", Consolas, monospace; font-size:12.5px; }
        .path { font-weight:700; font-size:14px; }
        .ep .body { padding:14px 18px; }
        .ep h4 { margin:16px 0 6px; font-size:12px; text-transform:uppercase; letter-spacing:.07em; color:var(--muted); }
        table { width:100%; border-collapse:collapse; font-size:13px; }
        th, td { text-align:left; padding:6px 8px; border-bottom:1px solid var(--line); vertical-align:top; }
        th { font-size:11px; text-transform:uppercase; color:var(--muted); }
        pre { background:#0f172a; color:#e2e8f0; padding:12px 14px; border-radius:8px; overflow-x:auto; margin:6px 0; }
        .st { display:inline-block; font-weight:800; font-size:12px; padding:1px 8px; border-radius:6px; margin-right:6px; }
        .s2 { background:#dcfce7; color:#166534; } .s4 { background:#fee2e2; color:#991b1b; }
        .note { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:8px 12px; font-size:13px; margin-top:12px; }
        .req { color:var(--red); font-weight:700; }
        @media (max-width: 860px) { aside { display:none; } main { padding:20px; } }
    </style>
</head>
<body>
<div class="layout">
    <aside>
        <strong>{{ $info['version'] }} · API REST</strong>
        @foreach ($groups as $group)
            <h2>{{ $group['name'] }}</h2>
            @foreach ($group['endpoints'] as $e)
                <a href="#{{ $e['id'] }}"><b>{{ $e['method'] }}</b> {{ $e['path'] }}</a>
            @endforeach
        @endforeach
        <h2>Descargas</h2>
        <a href="{{ route('docs.api.postman', 'collection') }}">Colección Postman</a>
        <a href="{{ route('docs.api.postman', 'environment') }}">Environment Postman</a>
    </aside>

    <main>
        <h1>{{ $info['title'] }}</h1>
        <p class="lead">{{ $info['description'] }}</p>

        <div class="box">
            <p><b>URL base:</b> <code>{{ $baseUrl }}</code></p>
            <p><b>Formato:</b> JSON (<code>Accept: application/json</code>). Paginación con <code>per_page</code> y <code>page</code>; la respuesta trae <code>data</code> y <code>meta</code>.</p>
            <p style="margin-bottom:0"><b>Autenticación privada:</b> obtén un token en <code>POST /auth/login</code> y envíalo como <code>Authorization: Bearer &lt;token&gt;</code>.</p>
        </div>

        <div class="box">
            <b>Códigos de estado usados:</b>
            200 OK · 201 Creado · 401 No autenticado · 403 Sin permiso · 404 No encontrado · 409 Conflicto con el estado actual · 422 Error de validación · 429 Demasiadas solicitudes.
        </div>

        @foreach ($groups as $group)
            <section class="group">
                <h2>{{ $group['name'] }} <span class="tag {{ $group['visibility'] }}">{{ $group['visibility'] === 'public' ? 'PÚBLICA' : 'PRIVADA' }}</span></h2>
                <p class="lead">{{ $group['description'] }}</p>

                @foreach ($group['endpoints'] as $e)
                    <article class="ep" id="{{ $e['id'] }}">
                        <header>
                            <span class="m {{ $e['method'] }}">{{ $e['method'] }}</span>
                            <span class="path">{{ $e['path'] }}</span>
                            <span class="tag {{ $e['auth'] ? 'private' : 'public' }}">{{ $e['auth'] ? 'Requiere token' : 'Sin token' }}</span>
                            @if ($e['roles']) <small style="color:var(--muted)">Roles: {{ implode(', ', $e['roles']) }}</small> @endif
                        </header>
                        <div class="body">
                            <p style="margin:0"><b>{{ $e['summary'] }}</b></p>

                            @if ($e['params'])
                                <h4>Parámetros</h4>
                                <table>
                                    <tr><th>Nombre</th><th>En</th><th>Tipo</th><th>Descripción</th><th>Ejemplo</th></tr>
                                    @foreach ($e['params'] as $p)
                                        <tr><td><code>{{ $p['name'] }}</code>@if ($p['required']) <span class="req">*</span>@endif</td><td>{{ $p['in'] }}</td><td>{{ $p['type'] }}</td><td>{{ $p['description'] }}</td><td><code>{{ is_scalar($p['example']) ? $p['example'] : '' }}</code></td></tr>
                                    @endforeach
                                </table>
                            @endif

                            @if ($e['body'])
                                <h4>Cuerpo ({{ $e['body_type'] === 'multipart' ? 'multipart/form-data' : 'application/json' }})</h4>
                                <table>
                                    <tr><th>Campo</th><th>Tipo</th><th>Descripción</th><th>Ejemplo</th></tr>
                                    @foreach ($e['body'] as $b)
                                        <tr><td><code>{{ $b['name'] }}</code>@if ($b['required']) <span class="req">*</span>@endif</td><td>{{ $b['type'] }}</td><td>{{ $b['description'] }}</td><td><code>{{ is_scalar($b['example']) ? $b['example'] : '' }}</code></td></tr>
                                    @endforeach
                                </table>
                            @endif

                            <h4>Ejemplo cURL</h4>
                            <pre>{{ str_replace('__BASE__', $baseUrl, $e['curl']) }}</pre>

                            <h4>Respuestas</h4>
                            @foreach ($e['responses'] as $r)
                                <p style="margin:8px 0 0"><span class="st {{ $r['status'] < 300 ? 's2' : 's4' }}">{{ $r['status'] }}</span> {{ $r['description'] }}</p>
                                @if ($r['example'])
                                    <pre>{{ json_encode($r['example'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                @endif
                            @endforeach

                            @if ($e['note']) <div class="note">{{ $e['note'] }}</div> @endif
                        </div>
                    </article>
                @endforeach
            </section>
        @endforeach
    </main>
</div>
</body>
</html>
