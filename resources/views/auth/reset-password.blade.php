<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prácticas Intermedias — Nueva contraseña</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-[Roboto]">

    <div class="bg-white p-6 rounded-lg shadow-sm w-full max-w-md text-center border border-gray-200">

        <div class="text-left -mt-1 mb-2">
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
            </a>
        </div>

        <!-- Logo Institucional -->
        <img src="https://upload.wikimedia.org/wikipedia/commons/7/7a/UCSC%2C_Universidad_Cat%C3%B3lica_de_la_Sant%C3%ADsima_Concepci%C3%B3n.png"
            alt="Logo Institucional" class="mx-auto mb-3 h-20 object-contain">

        <!-- Títulos -->
        <h3 class="text-xl font-bold text-gray-800 mb-1">Crear contraseña nueva</h3>
        <p class="text-gray-500 text-sm mb-4">Elige una contraseña de al menos 8 caracteres.</p>

        @if ($errors->any())
            <div class="bg-gray-50 border border-gray-800 text-gray-800 py-2 px-3 rounded text-sm text-left mb-4">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="bi bi-exclamation-triangle mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.store') }}" method="POST" class="text-left mt-2">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-4">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Correo electrónico:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="usuario@universidad.cl" required autofocus autocomplete="username">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Contraseña nueva:</label>
                <input type="password" id="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="******" required autocomplete="new-password">
            </div>

            <div class="mb-5">
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1">Confirmar contraseña:</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="******" required autocomplete="new-password">
            </div>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                GUARDAR CONTRASEÑA
            </button>
        </form>
    </div>

</body>

</html>
