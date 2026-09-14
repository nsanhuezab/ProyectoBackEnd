<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prácticas Intermedias — Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-[Roboto]">

    <div class="bg-white p-6 rounded-lg shadow-sm w-full max-w-md text-center border border-gray-200">

        <!-- Logo Institucional -->
        <img src="https://upload.wikimedia.org/wikipedia/commons/7/7a/UCSC%2C_Universidad_Cat%C3%B3lica_de_la_Sant%C3%ADsima_Concepci%C3%B3n.png"
            alt="Logo Institucional" class="mx-auto mb-3 h-20 object-contain">

        <!-- Títulos -->
        <h3 class="text-xl font-bold text-gray-800 mb-1">Prácticas Intermedias</h3>
        <p class="text-gray-500 text-sm mb-4">Plataforma de Gestión y Empleabilidad</p>


        @if (session('error'))
            <div
                class="bg-gray-50 border border-gray-800 text-gray-800 py-2 px-3 rounded text-sm text-left mb-4 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @elseif (session('msg') === 'sesion_cerrada')
            <div
                class="bg-gray-50 border border-gray-800 text-gray-800 py-2 px-3 rounded text-sm text-left mb-4 flex items-center gap-2">
                <i class="bi bi-check-circle"></i> Sesión cerrada correctamente.
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="text-left mt-2">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Correo electrónico:</label>
                <input type="email" name="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="usuario@universidad.cl" required autofocus>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-1">Contraseña:</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="******" required>
            </div>

            <button type="submit" id="btn-login"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                INICIAR SESIÓN
            </button>
        </form>

        <div class="mt-4">
            <a href="{{ route('password.request') }}" class="text-gray-800 text-sm hover:underline transition-colors">
                [ ¿Recuperar contraseña? ]
            </a>
        </div>
    </div>

</body>

</html>
