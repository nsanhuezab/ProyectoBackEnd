<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($info['title']); ?></title>
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
        <strong><?php echo e($info['version']); ?> · API REST</strong>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <h2><?php echo e($group['name']); ?></h2>
            <?php $__currentLoopData = $group['endpoints']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="#<?php echo e($e['id']); ?>"><b><?php echo e($e['method']); ?></b> <?php echo e($e['path']); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <h2>Descargas</h2>
        <a href="<?php echo e(route('docs.api.postman', 'collection')); ?>">Colección Postman</a>
        <a href="<?php echo e(route('docs.api.postman', 'environment')); ?>">Environment Postman</a>
    </aside>

    <main>
        <h1><?php echo e($info['title']); ?></h1>
        <p class="lead"><?php echo e($info['description']); ?></p>

        <div class="box">
            <p><b>URL base:</b> <code><?php echo e($baseUrl); ?></code></p>
            <p><b>Formato:</b> JSON (<code>Accept: application/json</code>). Paginación con <code>per_page</code> y <code>page</code>; la respuesta trae <code>data</code> y <code>meta</code>.</p>
            <p style="margin-bottom:0"><b>Autenticación privada:</b> obtén un token en <code>POST /auth/login</code> y envíalo como <code>Authorization: Bearer &lt;token&gt;</code>.</p>
        </div>

        <div class="box">
            <b>Códigos de estado usados:</b>
            200 OK · 201 Creado · 401 No autenticado · 403 Sin permiso · 404 No encontrado · 409 Conflicto con el estado actual · 422 Error de validación · 429 Demasiadas solicitudes.
        </div>

        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <section class="group">
                <h2><?php echo e($group['name']); ?> <span class="tag <?php echo e($group['visibility']); ?>"><?php echo e($group['visibility'] === 'public' ? 'PÚBLICA' : 'PRIVADA'); ?></span></h2>
                <p class="lead"><?php echo e($group['description']); ?></p>

                <?php $__currentLoopData = $group['endpoints']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="ep" id="<?php echo e($e['id']); ?>">
                        <header>
                            <span class="m <?php echo e($e['method']); ?>"><?php echo e($e['method']); ?></span>
                            <span class="path"><?php echo e($e['path']); ?></span>
                            <span class="tag <?php echo e($e['auth'] ? 'private' : 'public'); ?>"><?php echo e($e['auth'] ? 'Requiere token' : 'Sin token'); ?></span>
                            <?php if($e['roles']): ?> <small style="color:var(--muted)">Roles: <?php echo e(implode(', ', $e['roles'])); ?></small> <?php endif; ?>
                        </header>
                        <div class="body">
                            <p style="margin:0"><b><?php echo e($e['summary']); ?></b></p>

                            <?php if($e['params']): ?>
                                <h4>Parámetros</h4>
                                <table>
                                    <tr><th>Nombre</th><th>En</th><th>Tipo</th><th>Descripción</th><th>Ejemplo</th></tr>
                                    <?php $__currentLoopData = $e['params']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr><td><code><?php echo e($p['name']); ?></code><?php if($p['required']): ?> <span class="req">*</span><?php endif; ?></td><td><?php echo e($p['in']); ?></td><td><?php echo e($p['type']); ?></td><td><?php echo e($p['description']); ?></td><td><code><?php echo e(is_scalar($p['example']) ? $p['example'] : ''); ?></code></td></tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </table>
                            <?php endif; ?>

                            <?php if($e['body']): ?>
                                <h4>Cuerpo (<?php echo e($e['body_type'] === 'multipart' ? 'multipart/form-data' : 'application/json'); ?>)</h4>
                                <table>
                                    <tr><th>Campo</th><th>Tipo</th><th>Descripción</th><th>Ejemplo</th></tr>
                                    <?php $__currentLoopData = $e['body']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr><td><code><?php echo e($b['name']); ?></code><?php if($b['required']): ?> <span class="req">*</span><?php endif; ?></td><td><?php echo e($b['type']); ?></td><td><?php echo e($b['description']); ?></td><td><code><?php echo e(is_scalar($b['example']) ? $b['example'] : ''); ?></code></td></tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </table>
                            <?php endif; ?>

                            <h4>Ejemplo cURL</h4>
                            <pre><?php echo e(str_replace('__BASE__', $baseUrl, $e['curl'])); ?></pre>

                            <h4>Respuestas</h4>
                            <?php $__currentLoopData = $e['responses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p style="margin:8px 0 0"><span class="st <?php echo e($r['status'] < 300 ? 's2' : 's4'); ?>"><?php echo e($r['status']); ?></span> <?php echo e($r['description']); ?></p>
                                <?php if($r['example']): ?>
                                    <pre><?php echo e(json_encode($r['example'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></pre>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php if($e['note']): ?> <div class="note"><?php echo e($e['note']); ?></div> <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </main>
</div>
</body>
</html>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/docs/api.blade.php ENDPATH**/ ?>