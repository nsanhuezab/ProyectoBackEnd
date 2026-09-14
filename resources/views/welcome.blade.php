<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Prácticas Profesionales — UCSC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col justify-between font-[Roboto]">

    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/7a/UCSC%2C_Universidad_Cat%C3%B3lica_de_la_Sant%C3%ADsima_Concepci%C3%B3n.png"
                    alt="Logo UCSC" class="h-12 object-contain">
                <div>
                    <span class="font-bold text-gray-900 block text-lg">Prácticas Intermedias</span>
                    <span class="text-xs text-gray-500">Ingeniería de Ejecución en Informática</span>
                </div>
            </div>
            <div class="text-sm font-medium text-gray-500">
                Universidad Católica de la Santísima Concepción
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider">Gestión y Empleabilidad</span>
            <h1 class="text-4xl font-extrabold text-gray-900 mt-4 mb-6">
                Conectando el talento académico con el sector productivo
            </h1>
            <p class="text-lg text-gray-600 mb-8">
                Plataforma centralizada para la administración de prácticas profesionales. Gestiona ofertas, postulaciones en línea, bitácoras de avance y métricas de empleabilidad en un solo lugar.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-lg shadow transition-colors text-base">
                    Comenzar Ahora (Registrarse)
                </a>
                <a href="{{ route('login') }}" class="bg-white hover:bg-gray-100 text-gray-800 border border-gray-300 font-bold px-6 py-3 rounded-lg transition-colors text-base">
                    Iniciar Sesión
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-xl mb-4 font-bold">
                    <i class="bi bi-building"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Empresas Colaboradoras</h3>
                <p class="text-gray-600 text-sm">
                    Publicación de ofertas de práctica con especificación de perfiles, modalidades y cupos, validadas directamente por la coordinación académica.
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-xl mb-4 font-bold">
                    <i class="bi bi-cpu"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Matching Inteligente</h3>
                <p class="text-gray-600 text-sm">
                    Sugerencia automatizada de estudiantes más afines a cada vacante considerando carrera, nivel de avance curricular y habilidades declaradas.
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-xl mb-4 font-bold">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Indicadores y KPIs</h3>
                <p class="text-gray-600 text-sm">
                    Panel gerencial con tasas de aprobación, empleabilidad post-práctica y ranking de satisfacción de los centros evaluados por los estudiantes.
                </p>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500">
            <p>Universidad Católica de la Santísima Concepción</p>
        </div>
    </footer>

</body>

</html>
