<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prácticas Intermedias — Registro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-[Roboto]">

    <div class="bg-white p-6 rounded-lg shadow-sm w-full max-w-md mx-auto text-center border border-gray-200">

        <div class="flex justify-center mb-3">
        <img src="https://upload.wikimedia.org/wikipedia/commons/7/7a/UCSC%2C_Universidad_Cat%C3%B3lica_de_la_Sant%C3%ADsima_Concepci%C3%B3n.png"
            alt="Logo Institucional" class="mx-auto mb-3 h-20 object-contain">
        </div>

        <h3 class="text-xl font-bold text-gray-800 mb-1">Prácticas Intermedias</h3>
        <p class="text-gray-500 text-sm mb-4">Plataforma de Gestión y Empleabilidad</p>

        <form action="{{ route('register') }}" method="POST" class="text-left mt-2">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre(s):</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Ej. Juan Andrés" required autofocus>
                @error('nombre')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-2 mb-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Apellido Paterno:</label>
                    <input type="text" name="ApPat" value="{{ old('ApPat') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="Pérez" required>
                    @error('ApPat')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Apellido Materno:</label>
                    <input type="text" name="ApMat" value="{{ old('ApMat') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="González" required>
                    @error('ApMat')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Correo electrónico:</label>
                <input type="email" name="correo" value="{{ old('correo') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="usuario@universidad.cl" required>
                @error('correo')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Perfil (Rol) -->
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">¿Cómo deseas registrarte?</label>
                <select name="rol"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white"
                    required>
                    <option value="" disabled selected>Selecciona una opción...</option>
                    <option value="estudiante">Soy Estudiante</option>
                    <option value="empresa">Soy Empresa</option>
                </select>
                @error('rol')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Contraseña:</label>
                <input type="password" name="contrasena"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="******" required>
                @error('contrasena')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-1">Confirmar Contraseña:</label>
                <input type="password" name="contrasena_confirmation"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="******" required>
            </div>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                REGISTRARSE
            </button>
        </form>

        <div class="mt-4">
            <a href="{{ route('login') }}" class="text-gray-800 text-sm hover:underline transition-colors">
                [ ¿Ya tienes una cuenta? Inicia sesión ]
            </a>
        </div>
    </div>

</body>

</html>
