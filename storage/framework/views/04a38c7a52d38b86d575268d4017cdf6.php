<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prácticas Intermedias — Recuperar contraseña</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-[Roboto]">

    <div class="bg-white p-6 rounded-lg shadow-sm w-full max-w-md text-center border border-gray-200">

        <div class="text-left -mt-1 mb-2">
            <a href="<?php echo e(route('login')); ?>"
                class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
            </a>
        </div>

        <!-- Logo Institucional -->
        <img src="https://upload.wikimedia.org/wikipedia/commons/7/7a/UCSC%2C_Universidad_Cat%C3%B3lica_de_la_Sant%C3%ADsima_Concepci%C3%B3n.png"
            alt="Logo Institucional" class="mx-auto mb-3 h-20 object-contain">

        <!-- Títulos -->
        <h3 class="text-xl font-bold text-gray-800 mb-1">Recuperar contraseña</h3>
        <p class="text-gray-500 text-sm mb-4">
            Escribe el correo con el que te registraste y te enviaremos un enlace para crear una contraseña nueva.
        </p>

        <?php if(session('status')): ?>
            <div class="bg-green-50 border border-green-700 text-green-800 py-2 px-3 rounded text-sm text-left mb-4 flex items-start gap-2">
                <i class="bi bi-check-circle mt-0.5"></i> <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="bg-gray-50 border border-gray-800 text-gray-800 py-2 px-3 rounded text-sm text-left mb-4 flex items-start gap-2">
                <i class="bi bi-exclamation-triangle mt-0.5"></i> <?php echo e($message); ?>

            </div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <form action="<?php echo e(route('password.email')); ?>" method="POST" class="text-left mt-2">
            <?php echo csrf_field(); ?>

            <div class="mb-5">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Correo electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="usuario@universidad.cl" required autofocus>
            </div>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                ENVIAR ENLACE DE RECUPERACIÓN
            </button>
        </form>

        <div class="mt-4">
            <a href="<?php echo e(route('login')); ?>" class="text-gray-800 text-sm hover:underline transition-colors">
                [ ¿Recordaste tu contraseña? Inicia sesión ]
            </a>
        </div>
    </div>

</body>

</html>
<?php /**PATH C:\Users\nicoe\OneDrive\Escritorio\Proyecto BackEnd\ProyectoBackEnd\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>